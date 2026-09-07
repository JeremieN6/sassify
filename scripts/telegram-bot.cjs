require('dotenv').config({ path: require('path').join(__dirname, '..', '.env.local') });
const fs = require('fs');
const path = require('path');
const { execSync } = require('child_process');
const { shortId } = require('./article-id.cjs');
const { listerBrouillons, listerBloques, ROOT, BLOG_DIR, BACKLOG_FILE } = require('./article-status.cjs');
const { readMarkdownWithFrontmatter } = require('./lib/markdown-frontmatter.cjs');

const VIDEO_DIR = path.join(ROOT, 'content', 'video-scripts');

const API = (method) =>
  `https://api.telegram.org/bot${process.env.TELEGRAM_BOT_TOKEN}/${method}`;

let offset = 0;

async function call(method, body) {
  const res = await fetch(API(method), {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(body)
  });
  return res.json();
}

function boutonsArticle(id) {
  return [[
    { text: '✅ Publier', callback_data: `publish:${id}` },
    { text: '❌ Rejeter', callback_data: `reject:${id}` }
  ]];
}

function boutonSujetBloque(id) {
  return [[{ text: '🗑 Retirer du backlog', callback_data: `drop:${id}` }]];
}

function trouverSlugParId(id) {
  const fichiers = fs.readdirSync(BLOG_DIR).filter(f => f.endsWith('.md'));
  const match = fichiers.find(f => shortId(f.slice(0, -3)) === id);
  return match ? match.slice(0, -3) : null;
}

function traiterArticle(action, id) {
  const slug = trouverSlugParId(id);
  if (!slug) return { ok: false, msg: 'Article introuvable.' };

  const filePath = path.join(BLOG_DIR, `${slug}.md`);
  if (!fs.existsSync(filePath)) return { ok: false, msg: 'Fichier introuvable.' };

  const nouveauStatut = action === 'publish' ? 'publie' : 'rejete';
  const contenu = fs.readFileSync(filePath, 'utf-8')
    .replace('statut: brouillon', `statut: ${nouveauStatut}`);
  fs.writeFileSync(filePath, contenu, 'utf-8');

  try {
    execSync(`git add "${filePath}"`, { cwd: ROOT });
    execSync(`git commit -m "${action === 'publish' ? 'Publication' : 'Rejet'} : ${slug}"`, { cwd: ROOT });
    execSync('git push', { cwd: ROOT });
  } catch (err) {
    return { ok: false, msg: `Git a échoué : ${err.message.split('\n')[0]}` };
  }

  if (action === 'publish') {
    // Le script vidéo est un a-côté : s'il plante (API down, article mal
    // forme...), la publication de l'article ne doit jamais en dépendre.
    try {
      execSync(`node "${path.join(ROOT, 'scripts', 'generate-video-script.cjs')}" ${slug}`, { cwd: ROOT, stdio: 'pipe' });
    } catch (err) {
      console.error(`Génération du script vidéo échouée pour ${slug} :`, err.message);
    }
  }

  return { ok: true, msg: action === 'publish' ? `✅ Publié : ${slug}` : `❌ Rejeté : ${slug}` };
}

function traiterSujetBloque(id) {
  const backlog = JSON.parse(fs.readFileSync(BACKLOG_FILE, 'utf-8'));
  const sujet = backlog.find(s => s.statut === 'bloque' && shortId(s.titre) === id);
  if (!sujet) return { ok: false, msg: 'Sujet introuvable ou déjà traité.' };

  sujet.statut = 'ecarte';
  fs.writeFileSync(BACKLOG_FILE, JSON.stringify(backlog, null, 2), 'utf-8');

  try {
    execSync('git add content/backlog.json', { cwd: ROOT });
    execSync(`git commit -m "Sujet écarté : ${sujet.titre}"`, { cwd: ROOT });
    execSync('git push', { cwd: ROOT });
  } catch (err) {
    return { ok: false, msg: `Git a échoué : ${err.message.split('\n')[0]}` };
  }

  return { ok: true, msg: `🗑 Écarté du backlog : ${sujet.titre}` };
}

async function envoyerStatus(chatId) {
  const brouillons = listerBrouillons();
  const bloques = listerBloques();

  if (brouillons.length === 0 && bloques.length === 0) {
    await call('sendMessage', { chat_id: chatId, text: '✅ Rien en attente — tout ce qui devait être publié l\'est.' });
    return;
  }

  for (const b of brouillons) {
    await call('sendMessage', {
      chat_id: chatId,
      text: `📝 En brouillon, en attente de ta validation :\n\n*${b.titre}*`,
      parse_mode: 'Markdown',
      reply_markup: { inline_keyboard: boutonsArticle(shortId(b.slug)) }
    });
  }

  for (const s of bloques) {
    await call('sendMessage', {
      chat_id: chatId,
      text: `🛑 Bloqué par les garde-fous, rien n'a été généré :\n\n*${s.titre}*\n\n${s.raisonBlocage || 'Raison non enregistrée.'}\n\nPour le débloquer : soit reformuler l'angle du sujet dans content/backlog.json (nouvel essai au prochain run), soit ajuster un garde-fou dans knowledge-base.md / generate-article.cjs si le refus est trop strict. Pas d'action "publier" possible ici, aucun article n'existe.`,
      parse_mode: 'Markdown',
      reply_markup: { inline_keyboard: boutonSujetBloque(shortId(s.titre)) }
    });
  }
}

// La validation et la publication de la video restent entierement dans
// Plotline (boutons Valider/Publier deja existants sur "Mes creations") --
// ce bot ne fait qu'avertir que c'est pret, il ne decide de rien. Genere par
// generate-video-script.cjs, qui pousse le job a Plotline de facon
// asynchrone : la generation prend 1-2 minutes, donc on ne peut pas attendre
// la reponse dans le meme appel sans bloquer la boucle Telegram. On verifie
// ici, a chaque tour de boucle, si un job en attente est termine.
async function verifierVideosPretes() {
  const apiKey = process.env.PLOTLINE_API_KEY;
  const baseUrl = process.env.PLOTLINE_BASE_URL || 'https://plotline.sassify.fr';
  if (!apiKey || !fs.existsSync(VIDEO_DIR)) return;

  const fichiers = fs.readdirSync(VIDEO_DIR).filter(f => f.endsWith('.md'));

  for (const fichier of fichiers) {
    const filePath = path.join(VIDEO_DIR, fichier);
    let frontmatter;
    try {
      ({ frontmatter } = readMarkdownWithFrontmatter(filePath));
    } catch {
      continue;
    }

    // "demande" = job pousse a Plotline, pas encore de reponse connue.
    // Tout autre statut (non_configure, echec, pret, echec_generation) est
    // deja stable : rien a revalider a chaque tour de boucle.
    if (frontmatter.plotlineStatus !== 'demande' || !frontmatter.plotlineContentId) continue;

    let statusData;
    try {
      const res = await fetch(`${baseUrl}/api/external/video-jobs/${frontmatter.plotlineContentId}/status`, {
        headers: { 'x-api-key': apiKey },
      });
      if (!res.ok) continue; // reessaie au prochain tour
      statusData = await res.json();
    } catch {
      continue; // panne reseau transitoire, on reessaie au prochain tour
    }

    if (!statusData.done) continue;

    const slug = frontmatter.sourceSlug || fichier.replace(/\.md$/, '');
    const titre = frontmatter.titre || slug;
    const contenu = fs.readFileSync(filePath, 'utf-8');

    if (statusData.failed) {
      fs.writeFileSync(filePath, contenu.replace('plotlineStatus: demande', 'plotlineStatus: echec_generation'), 'utf-8');
      await call('sendMessage', {
        chat_id: process.env.TELEGRAM_CHAT_ID,
        text: `❌ Vidéo échouée pour "${titre}" : ${statusData.errorMessage || 'raison inconnue'}`,
      });
    } else {
      fs.writeFileSync(filePath, contenu.replace('plotlineStatus: demande', 'plotlineStatus: pret'), 'utf-8');
      await call('sendMessage', {
        chat_id: process.env.TELEGRAM_CHAT_ID,
        text: `🎥 Vidéo prête pour "${titre}" — à valider et publier depuis Plotline.`,
      });
    }

    try {
      execSync(`git add "${filePath}"`, { cwd: ROOT });
      execSync(`git commit -m "Statut video mis a jour : ${slug}"`, { cwd: ROOT });
      execSync('git push', { cwd: ROOT });
    } catch (err) {
      console.error(`Git a echoue pour le statut video de ${slug} :`, err.message);
    }
  }
}

async function boucle() {
  try {
    await verifierVideosPretes();
  } catch (err) {
    console.error('Verification des videos en attente echouee :', err.message);
  }

  try {
    const res = await fetch(API('getUpdates') + `?offset=${offset}&timeout=30`);
    const data = await res.json();

    if (data.ok) {
      for (const update of data.result) {
        offset = update.update_id + 1;

        if (update.callback_query) {
          const query = update.callback_query;
          const [action, id] = query.data.split(':');
          const resultat = action === 'drop' ? traiterSujetBloque(id) : traiterArticle(action, id);

          await call('answerCallbackQuery', {
            callback_query_id: query.id,
            text: resultat.ok ? 'C\'est fait' : resultat.msg
          });
          await call('sendMessage', {
            chat_id: query.message.chat.id,
            text: resultat.msg
          });
          console.log(resultat.msg);
          continue;
        }

        if (update.message && typeof update.message.text === 'string') {
          const commande = update.message.text.trim().split('@')[0];
          if (commande === '/status' || commande === '/brouillons') {
            await envoyerStatus(update.message.chat.id);
            console.log(`✅ /status envoyé à ${update.message.chat.id}`);
          }
        }
      }
    }
  } catch (err) {
    console.error('Erreur de boucle :', err.message);
    await new Promise(r => setTimeout(r, 5000));
  }
  boucle();
}

console.log('🤖 Bot Telegram en écoute...');
boucle();
