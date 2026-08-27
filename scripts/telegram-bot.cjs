require('dotenv').config({ path: '.env.local' });
const TelegramBot = require('node-telegram-bot-api');
const fs = require('fs');
const path = require('path');
const { execSync } = require('child_process');

const ROOT = path.join(__dirname, '..');
const BLOG_DIR = path.join(ROOT, 'content', 'blog');

const bot = new TelegramBot(process.env.TELEGRAM_BOT_TOKEN, { polling: true });

bot.on('callback_query', (query) => {
  const [action, slug] = query.data.split(':');
  const filePath = path.join(BLOG_DIR, `${slug}.md`);

  if (!fs.existsSync(filePath)) {
    bot.answerCallbackQuery(query.id, { text: 'Fichier introuvable.' });
    return;
  }

  let contenu = fs.readFileSync(filePath, 'utf-8');

  if (action === 'publish') {
    contenu = contenu.replace('statut: brouillon', 'statut: publie');
    fs.writeFileSync(filePath, contenu, 'utf-8');
    execSync(`git add "${filePath}" && git commit -m "Publication : ${slug}" && git push`, { cwd: ROOT, shell: 'powershell.exe' });
    bot.answerCallbackQuery(query.id, { text: 'Publié ✅' });
    bot.sendMessage(query.message.chat.id, `✅ Publié : ${slug}`);
  } else if (action === 'reject') {
    contenu = contenu.replace('statut: brouillon', 'statut: rejete');
    fs.writeFileSync(filePath, contenu, 'utf-8');
    execSync(`git add "${filePath}" && git commit -m "Rejet : ${slug}" && git push`, { cwd: ROOT, shell: 'powershell.exe' });
    bot.answerCallbackQuery(query.id, { text: 'Rejeté ❌' });
    bot.sendMessage(query.message.chat.id, `❌ Rejeté : ${slug}`);
  }
});

console.log('🤖 Bot Telegram en écoute...');