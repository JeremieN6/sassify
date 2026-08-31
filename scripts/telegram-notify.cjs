require('dotenv').config({ path: require('path').join(__dirname, '..', '.env.local') });
const { shortId } = require('./article-id.cjs');

const API = (method) =>
  `https://api.telegram.org/bot${process.env.TELEGRAM_BOT_TOKEN}/${method}`;

async function envoyerAvecBoutons({ slug, texte }) {
  const id = shortId(slug);
  const res = await fetch(API('sendMessage'), {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      chat_id: process.env.TELEGRAM_CHAT_ID,
      text: texte,
      parse_mode: 'Markdown',
      reply_markup: {
        inline_keyboard: [[
          { text: '✅ Publier', callback_data: `publish:${id}` },
          { text: '❌ Rejeter', callback_data: `reject:${id}` }
        ]]
      }
    })
  });

  const data = await res.json();
  if (!data.ok) throw new Error(`Telegram: ${data.description}`);
  return data;
}

async function notifierNouvelArticle({ slug, titre }) {
  return envoyerAvecBoutons({ slug, texte: `Nouvel article généré :\n\n*${titre}*` });
}

async function notifierRappelEnAttente({ slug, titre }) {
  return envoyerAvecBoutons({
    slug,
    texte: `⏳ Rappel : un article attend toujours ta validation :\n\n*${titre}*\n\nAucun nouvel article ne sera généré tant que celui-ci n'est pas publié ou rejeté.`
  });
}

async function notifierSujetBloque({ titre, raison }) {
  const res = await fetch(API('sendMessage'), {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      chat_id: process.env.TELEGRAM_CHAT_ID,
      text: `🛑 Sujet bloqué par les garde-fous :\n\n*${titre}*\n\n${raison}\n\nÀ retravailler manuellement dans content/backlog.json (statut actuel : \`bloque\`).`,
      parse_mode: 'Markdown'
    })
  });

  const data = await res.json();
  if (!data.ok) throw new Error(`Telegram: ${data.description}`);
  return data;
}

module.exports = { notifierNouvelArticle, notifierSujetBloque, notifierRappelEnAttente };