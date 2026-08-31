require('dotenv').config({ path: require('path').join(__dirname, '..', '.env.local') });
const fs = require('fs');
const path = require('path');
const { execSync } = require('child_process');
const { shortId } = require('./article-id.cjs');
const { listerBrouillons, listerBloques, ROOT, BLOG_DIR, BACKLOG_FILE } = require('./article-status.cjs');

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

async function boucle() {
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
