const TelegramBotModule = require('node-telegram-bot-api');
const TelegramBot = TelegramBotModule.default || TelegramBotModule;

function notifierNouvelArticle({ slug, titre }) {
  const bot = new TelegramBot(process.env.TELEGRAM_BOT_TOKEN);
  const extrait = `Nouvel article généré :\n\n*${titre}*`;

  return bot.sendMessage(process.env.TELEGRAM_CHAT_ID, extrait, {
    parse_mode: 'Markdown',
    reply_markup: {
      inline_keyboard: [[
        { text: '✅ Publier', callback_data: `publish:${slug}` },
        { text: '❌ Rejeter', callback_data: `reject:${slug}` }
      ]]
    }
  });
}

module.exports = { notifierNouvelArticle };