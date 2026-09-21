#!/usr/bin/env node
require('dotenv').config({ path: require('path').join(__dirname, '..', '.env.local') });
const fs = require('fs');
const path = require('path');
const Anthropic = require('@anthropic-ai/sdk');

const ROOT = path.join(__dirname, '..');
const BLOG_DIR = path.join(ROOT, 'content', 'blog');
const VIDEO_DIR = path.join(ROOT, 'content', 'video-scripts');
const KB_STATIC = path.join(ROOT, 'content', 'knowledge-base.md');
const DECORS_FILE = path.join(ROOT, 'config', 'decors.json');

// Deux formats de contraintes. "court" : une seule génération Omni Flash de
// 10s. "long" : Plotline découpe le script PAR PHRASE et enchaîne jusqu'à 4
// segments de 10s (plafond dur Google : 40s cumulés). Un script long doit donc
// tenir en 4 phrases d'environ 20-24 mots : au-delà, Plotline tronque la fin --
// donc la chute -- (constaté avec l'ancien format 90-140 mots : 61 mots gardés
// sur 138). Bascule avec VIDEO_SCRIPT_MODE=long ; "court" par défaut.
const MODES = {
  court: {
    label: 'court (10s, test Omni Flash)',
    minMots: 15,
    maxMots: 25,
    contraintes: `Tu transformes un article de blog en script parlé pour une vidéo TRÈS courte (10 secondes maximum, contrainte technique stricte du modèle vidéo utilisé).
Le persona à l'écran répond directement à la question affichée en haut de l'écran (fournie séparément, ne la répète pas).
Contraintes : 15 à 25 mots MAXIMUM, une seule phrase choc et percutante — pas un développement, pas d'explication complète, juste l'essentiel qui donne envie d'aller lire l'article complet. Tutoiement, adresse directe à la caméra. Ton avec du caractère, jamais dramatisé ni auto-dénigrant. Termine sur une chute nette. Respecte les garde-fous de knowledge-base.md.`,
  },
  long: {
    label: 'long (4 phrases, ~40s)',
    minMots: 70,
    maxMots: 96,
    contraintes: `Tu transformes un article de blog en script parlé pour une vidéo courte (40 secondes maximum, contrainte technique stricte du modèle vidéo utilisé).
Le persona à l'écran répond directement à la question affichée en haut de l'écran (fournie séparément, ne la répète pas).
Contraintes : EXACTEMENT 4 phrases, de 18 à 24 mots chacune (70 à 96 mots au total), chaque phrase se terminant par un point et se comprenant seule à l'oral. Structure : 1) le contexte ou la galère, 2) la décision ou ce que tu as compris, 3) le résultat concret, 4) une chute nette qui donne envie d'aller lire l'article complet. Condensé à l'essentiel, jamais une lecture du texte original. Tutoiement, adresse directe à la caméra. Ton avec du caractère, jamais dramatisé ni auto-dénigrant. Respecte les garde-fous de knowledge-base.md.`,
  },
};

const MODE = MODES[process.env.VIDEO_SCRIPT_MODE] ? process.env.VIDEO_SCRIPT_MODE : 'court';
const { label, minMots, maxMots, contraintes } = MODES[MODE];

const SYSTEM_PROMPT = `${contraintes}

Si le script ne peut pas être écrit sans violer un garde-fou de knowledge-base.md — quel que soit l'angle — ne rédige rien. Réponds uniquement par :

REFUS: une ou deux phrases expliquant précisément quel garde-fou est en cause.

Sinon, réponds uniquement avec le texte du script parlé, rien d'autre : pas de titre, pas de guillemets, pas de commentaire, pas de didascalie.`;

function parseArticleCandidat(fichier) {
  const filePath = path.join(BLOG_DIR, fichier);
  const contenu = fs.readFileSync(filePath, 'utf-8');
  if (!/^statut:\s*publie\s*$/m.test(contenu)) return null;

  const slug = fichier.slice(0, -3);
  const titreMatch = contenu.match(/^title:\s*"?(.*?)"?\s*$/m);
  const hookMatch = contenu.match(/^hookVideo:\s*"?(.*?)"?\s*$/m);
  const pilierMatch = contenu.match(/^pilier:\s*"?(.*?)"?\s*$/m);
  const corps = contenu.replace(/^---[\s\S]*?---\s*/, '').trim();

  return {
    slug,
    titre: titreMatch ? titreMatch[1] : slug,
    hookVideo: hookMatch ? hookMatch[1] : '',
    pilier: pilierMatch ? pilierMatch[1] : '',
    corps,
    mtime: fs.statSync(filePath).mtimeMs,
  };
}

function trouverArticleSansScript() {
  if (!fs.existsSync(BLOG_DIR)) return null;
  const fichiers = fs.readdirSync(BLOG_DIR).filter(f => f.endsWith('.md'));

  const candidats = fichiers
    .map(fichier => {
      const article = parseArticleCandidat(fichier);
      if (!article) return null;
      if (fs.existsSync(path.join(VIDEO_DIR, `${article.slug}.md`))) return null; // déjà traité
      return article;
    })
    .filter(Boolean)
    .sort((a, b) => a.mtime - b.mtime);

  return candidats[0] || null;
}

// Override manuel pour cibler un article precis (tests, rattrapage) plutot que
// de subir la selection automatique par date de modification -- inutile pour
// le cron du lundi, qui n'a jamais besoin de choisir un article en particulier.
function trouverArticleParSlug(slug) {
  const fichier = `${slug}.md`;
  if (!fs.existsSync(path.join(BLOG_DIR, fichier))) {
    console.error(`❌ Article introuvable : ${slug}`);
    process.exit(1);
  }

  const article = parseArticleCandidat(fichier);
  if (!article) {
    console.error(`❌ "${slug}" n'est pas en statut "publie" -- impossible d'en tirer un script vidéo.`);
    process.exit(1);
  }

  return article;
}

function resoudreDecor(pilier) {
  if (!fs.existsSync(DECORS_FILE)) return '';
  const decors = JSON.parse(fs.readFileSync(DECORS_FILE, 'utf-8'));
  return decors[pilier] || '';
}

// Le script parle est genere ici, mais la video elle-meme est produite par
// Plotline (pipeline persona/identity lock deja eprouve la-bas). Un echec de
// cette etape ne doit jamais faire perdre le script deja ecrit et commit :
// on le journalise dans le frontmatter plutot que de faire echouer tout le
// script (meme logique que le hook telegram-bot.cjs -> generate-video-script.cjs,
// qui traite deja cette generation comme un a-cote non bloquant).
async function demanderVideoAPlotline({ slug, decorPrompt, scriptText }) {
  const apiKey = process.env.PLOTLINE_API_KEY;
  // Profil Plotline qui RECOIT la video (marque, persona...): un dossier de
  // rangement, pas la personne a l'ecran (le personnage est invente par le
  // modele). PLOTLINE_INFLUENCER_ID est l'ancien nom de la variable, toujours lu.
  const profileId = process.env.PLOTLINE_PROFILE_ID || process.env.PLOTLINE_INFLUENCER_ID;
  const baseUrl = process.env.PLOTLINE_BASE_URL || 'https://plotline.sassify.fr';

  if (!apiKey || !profileId) {
    return { statut: 'non_configure', raison: 'PLOTLINE_API_KEY ou PLOTLINE_PROFILE_ID manquant dans .env.local' };
  }

  try {
    const response = await fetch(`${baseUrl}/api/external/video-jobs`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'x-api-key': apiKey },
      // `influencerId` = ancien nom du parametre, envoye en double tant que
      // Plotline n'est pas deploye avec `profileId` (a retirer ensuite).
      body: JSON.stringify({ profileId, influencerId: profileId, decorPrompt, scriptText, slug, sourceProject: 'home' }),
    });

    const data = await response.json().catch(() => ({}));
    if (!response.ok) {
      return { statut: 'echec', raison: `${response.status} ${data?.statusMessage || data?.message || 'erreur inconnue'}` };
    }

    return { statut: 'demande', contentId: data.contentId };
  } catch (err) {
    return { statut: 'echec', raison: err.message };
  }
}

async function main() {
  if (!process.env.ANTHROPIC_API_KEY) {
    console.error('❌ ANTHROPIC_API_KEY manquant dans .env.local');
    process.exit(1);
  }

  console.log(`🎬 Mode : ${label}`);

  const slugImpose = process.argv[2];
  const article = slugImpose ? trouverArticleParSlug(slugImpose) : trouverArticleSansScript();
  if (!article) {
    console.log('ℹ️  Aucun article publié sans script vidéo en attente.');
    return;
  }

  if (!article.hookVideo) {
    console.error(`❌ "${article.titre}" n'a pas de hookVideo en frontmatter — impossible de générer un script sans la question affichée à l'écran.`);
    process.exit(1);
  }

  console.log(`📝 Génération du script vidéo pour : "${article.titre}"`);

  const kbStatic = fs.readFileSync(KB_STATIC, 'utf-8');
  const client = new Anthropic({ apiKey: process.env.ANTHROPIC_API_KEY });

  const userPrompt = `${kbStatic}\n\n---\n\nArticle source :\nTitre : ${article.titre}\nQuestion affichée à l'écran (hookVideo, ne pas la répéter dans le script) : ${article.hookVideo}\n\nCorps de l'article :\n${article.corps}`;

  const response = await client.messages.create({
    model: 'claude-sonnet-4-6',
    max_tokens: 500,
    system: SYSTEM_PROMPT,
    messages: [{ role: 'user', content: userPrompt }]
  });

  const rawText = response.content.map(b => b.text || '').join('\n').trim();

  if (/^REFUS\s*:/i.test(rawText)) {
    const raison = rawText.replace(/^REFUS\s*:\s*/i, '').trim();
    console.log(`🛑 Le modèle a refusé de générer ce script : ${raison}`);
    return { bloque: true, titre: article.titre, raison };
  }

  const script = rawText.trim();
  const nbMots = script.split(/\s+/).filter(Boolean).length;
  if (nbMots < minMots || nbMots > maxMots) {
    console.warn(`⚠️  Le script fait ${nbMots} mots, hors de la cible ${minMots}-${maxMots} pour le mode "${MODE}" (généré quand même).`);
  }

  const decorPrompt = resoudreDecor(article.pilier);
  if (!decorPrompt) {
    console.warn(`⚠️  Aucun décor connu pour le pilier "${article.pilier}" -- la vidéo sera demandée sans décor spécifique.`);
  }

  if (MODE === 'long') {
    const nbPhrases = script.split(/(?<=[.!?])\s+/).filter(Boolean).length;
    if (nbPhrases > 4) {
      console.warn(`⚠️  Mode "long" : ${nbPhrases} phrases générées, Plotline n'en enchaîne que 4 (40s max) -- la fin du script sera tronquée.`);
    }
  }

  console.log('🎥 Demande de génération vidéo à Plotline...');
  const plotlineResult = await demanderVideoAPlotline({ slug: article.slug, decorPrompt, scriptText: script });

  if (plotlineResult.statut === 'demande') {
    console.log(`✅ Vidéo demandée (contentId Plotline : ${plotlineResult.contentId})`);
  } else if (plotlineResult.statut === 'non_configure') {
    console.warn(`⚠️  ${plotlineResult.raison} -- script généré mais aucune vidéo demandée.`);
  } else {
    console.error(`❌ Échec de la demande vidéo à Plotline : ${plotlineResult.raison}`);
  }

  const frontmatter = `---
sourceSlug: ${article.slug}
titre: "${article.titre.replace(/"/g, '\\"')}"
mode: ${MODE}
nbMots: ${nbMots}
dateGeneration: ${new Date().toISOString().split('T')[0]}
plotlineStatus: ${plotlineResult.statut}
plotlineContentId: ${plotlineResult.contentId || ''}
---

`;

  fs.mkdirSync(VIDEO_DIR, { recursive: true });
  const outputPath = path.join(VIDEO_DIR, `${article.slug}.md`);
  fs.writeFileSync(outputPath, frontmatter + script + '\n', 'utf-8');

  console.log(`✅ Script vidéo généré (${nbMots} mots, mode ${MODE}) : ${outputPath}`);

  const { execSync } = require('child_process');
  execSync(`git add "${outputPath}"`, { cwd: ROOT });
  execSync(`git commit -m "Script vidéo généré : ${article.titre}"`, { cwd: ROOT });
  execSync('git push', { cwd: ROOT });
  console.log('✅ Commit + push effectués');

  return { slug: article.slug, script, nbMots, mode: MODE };
}

main().catch(err => {
  console.error('❌ Erreur :', err.message);
  process.exit(1);
});
