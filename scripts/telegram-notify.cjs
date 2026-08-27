require('dotenv').config({ path: require('path').join(__dirname, '..', '.env.local') });

const API = (method) =>
  `https://api.telegram.org/bot${process.env.TELEGRAM_BOT_TOKEN}/${method}`;

async function notifierNouvelArticle({ slug, titre }) {
  const res = await fetch(API('sendMessage'), {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      chat_id: process.env.TELEGRAM_CHAT_ID,
      text: `Nouvel article généré :\n\n*${titre}*`,
      parse_mode: 'Markdown',
      reply_markup: {
        inline_keyboard: [[
          { text: '✅ Publier', callback_data: `publish:${slug}` },
          { text: '❌ Rejeter', callback_data: `reject:${slug}` }
        ]]
      }
    })
  });

  const data = await res.json();
  if (!data.ok) throw new Error(`Telegram: ${data.description}`);
  return data;
}

module.exports = { notifierNouvelArticle };