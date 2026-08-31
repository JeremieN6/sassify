#!/usr/bin/env node
// Publie ou rejette manuellement un article en attente, sans passer par Telegram.
// Usage : node scripts/decide-article.cjs <slug> <publish|reject>
const fs = require('fs');
const path = require('path');
const { execSync } = require('child_process');

const ROOT = path.join(__dirname, '..');
const BLOG_DIR = path.join(ROOT, 'content', 'blog');

const [, , slug, action] = process.argv;

if (!slug || !['publish', 'reject'].includes(action)) {
  console.error('Usage : node scripts/decide-article.cjs <slug> <publish|reject>');
  process.exit(1);
}

const filePath = path.join(BLOG_DIR, `${slug}.md`);
if (!fs.existsSync(filePath)) {
  console.error(`❌ Fichier introuvable : ${filePath}`);
  process.exit(1);
}

const contenu = fs.readFileSync(filePath, 'utf-8');
if (!/^statut:\s*brouillon\s*$/m.test(contenu)) {
  console.error('❌ Cet article n\'est pas en statut "brouillon" — rien à faire.');
  process.exit(1);
}

const nouveauStatut = action === 'publish' ? 'publie' : 'rejete';
fs.writeFileSync(filePath, contenu.replace('statut: brouillon', `statut: ${nouveauStatut}`), 'utf-8');

execSync(`git add "${filePath}"`, { cwd: ROOT });
execSync(`git commit -m "${action === 'publish' ? 'Publication' : 'Rejet'} : ${slug}"`, { cwd: ROOT });
execSync('git push', { cwd: ROOT });

console.log(action === 'publish' ? `✅ Publié : ${slug}` : `❌ Rejeté : ${slug}`);
