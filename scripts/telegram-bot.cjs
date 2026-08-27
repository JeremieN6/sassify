require('dotenv').config({ path: require('path').join(__dirname, '..', '.env.local') });
const fs = require('fs');
const path = require('path');
const { execSync } = require('child_process');

const ROOT = path.join(__dirname, '..');
const BLOG_DIR = path.join(ROOT, 'content', 'blog');
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

function traiter(action, slug) {
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

async function boucle() {
  try {
    const res = await fetch(API('getUpdates') + `?offset=${offset}&timeout=30`);
    const data = await res.json();

    if (data.ok) {
      for (const update of data.result) {
        offset = update.update_id + 1;
        if (!update.callback_query) continue;

        const query = update.callback_query;
        const [action, slug] = query.data.split(':');
        const resultat = traiter(action, slug);

        await call('answerCallbackQuery', {
          callback_query_id: query.id,
          text: resultat.ok ? 'C\'est fait' : resultat.msg
        });
        await call('sendMessage', {
          chat_id: query.message.chat.id,
          text: resultat.msg
        });
        console.log(resultat.msg);
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