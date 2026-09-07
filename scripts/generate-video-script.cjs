#!/usr/bin/env node
require('dotenv').config({ path: require('path').join(__dirname, '..', '.env.local') });
const fs = require('fs');
const path = require('path');
const Anthropic = require('@anthropic-ai/sdk');

const ROOT = path.join(__dirname, '..');
const BLOG_DIR = path.join(ROOT, 'content', 'blog');
const VIDEO_DIR = path.join(ROOT, 'content', 'video-scripts');
const KB_STATIC = path.join(ROOT, 'content', 'knowledge-base.md');

// Deux formats de contraintes. "court" est le format de test actuel : le
// modèle vidéo utilisé pour l'instant (Omni Flash) ne génère que 10s par
// appel et c'est coûteux, donc on ne teste qu'avec une seule vidéo courte.
// "long" est l'objectif réel une fois qu'on ne sera plus limité à ces
// générations de 10s. Bascule avec VIDEO_SCRIPT_MODE=long ; "court" par
// défaut.
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
    label: 'long (40-60s, objectif cible)',
    minMots: 90,
    maxMots: 140,
    contraintes: `Tu transformes un article de blog en script parlé pour une vidéo courte (40 à 60 secondes).
Le persona à l'écran répond directement à la question affichée en haut de l'écran (fournie séparément, ne la répète pas).
Contraintes : 90 à 140 mots, structuré comme l'article — contexte → décision/galère → résultat concret — mais condensé à l'essentiel, jamais une lecture du texte original. Tutoiement, adresse directe à la caméra. Ton avec du caractère, jamais dramatisé ni auto-dénigrant. Termine sur une chute nette qui donne envie d'aller lire l'article complet. Respecte les garde-fous de knowledge-base.md.`,
  },
};

const MODE = MODES[process.env.VIDEO_SCRIPT_MODE] ? process.env.VIDEO_SCRIPT_MODE : 'court';
const { label, minMots, maxMots, contraintes } = MODES[MODE];

const SYSTEM_PROMPT = `${contraintes}

Si le script ne peut pas être écrit sans violer un garde-fou de knowledge-base.md — quel que soit l'angle — ne rédige rien. Réponds uniquement par :

REFUS: une ou deux phrases expliquant précisément quel garde-fou est en cause.

Sinon, réponds uniquement avec le texte du script parlé, rien d'autre : pas de titre, pas de guillemets, pas de commentaire, pas de didascalie.`;

function trouverArticleSansScript() {
  if (!fs.existsSync(BLOG_DIR)) return null;
  const fichiers = fs.readdirSync(BLOG_DIR).filter(f => f.endsWith('.md'));

  const candidats = fichiers
    .map(fichier => {
      const filePath = path.join(BLOG_DIR, fichier);
      const contenu = fs.readFileSync(filePath, 'utf-8');
      if (!/^statut:\s*publie\s*$/m.test(contenu)) return null;

      const slug = fichier.slice(0, -3);
      if (fs.existsSync(path.join(VIDEO_DIR, `${slug}.md`))) return null; // déjà traité

      const titreMatch = contenu.match(/^title:\s*"?(.*?)"?\s*$/m);
      const hookMatch = contenu.match(/^hookVideo:\s*"?(.*?)"?\s*$/m);
      const corps = contenu.replace(/^---[\s\S]*?---\s*/, '').trim();

      return {
        slug,
        titre: titreMatch ? titreMatch[1] : slug,
        hookVideo: hookMatch ? hookMatch[1] : '',
        corps,
        mtime: fs.statSync(filePath).mtimeMs,
      };
    })
    .filter(Boolean)
    .sort((a, b) => a.mtime - b.mtime);

  return candidats[0] || null;
}

async function main() {
  if (!process.env.ANTHROPIC_API_KEY) {
    console.error('❌ ANTHROPIC_API_KEY manquant dans .env.local');
    process.exit(1);
  }

  console.log(`🎬 Mode : ${label}`);

  const article = trouverArticleSansScript();
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

  const frontmatter = `---
sourceSlug: ${article.slug}
titre: "${article.titre.replace(/"/g, '\\"')}"
mode: ${MODE}
nbMots: ${nbMots}
dateGeneration: ${new Date().toISOString().split('T')[0]}
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
