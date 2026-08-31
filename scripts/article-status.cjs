const fs = require('fs');
const path = require('path');

const ROOT = path.join(__dirname, '..');
const BLOG_DIR = path.join(ROOT, 'content', 'blog');
const BACKLOG_FILE = path.join(ROOT, 'content', 'backlog.json');

// Articles générés, en attente de publication ou de rejet.
function listerBrouillons() {
  if (!fs.existsSync(BLOG_DIR)) return [];
  const fichiers = fs.readdirSync(BLOG_DIR).filter(f => f.endsWith('.md'));

  return fichiers
    .map(fichier => {
      const filePath = path.join(BLOG_DIR, fichier);
      const contenu = fs.readFileSync(filePath, 'utf-8');
      if (!/^statut:\s*brouillon\s*$/m.test(contenu)) return null;
      const titreMatch = contenu.match(/^title:\s*"?(.*?)"?\s*$/m);
      return {
        slug: fichier.slice(0, -3),
        titre: titreMatch ? titreMatch[1] : fichier,
        mtime: fs.statSync(filePath).mtimeMs,
      };
    })
    .filter(Boolean)
    .sort((a, b) => a.mtime - b.mtime);
}

// Sujets du backlog refusés par les garde-fous : rien n'a été généré,
// il faut soit reformuler l'angle dans le backlog, soit assouplir un
// garde-fou dans le code (knowledge-base.md / generate-article.cjs).
function listerBloques() {
  if (!fs.existsSync(BACKLOG_FILE)) return [];
  const backlog = JSON.parse(fs.readFileSync(BACKLOG_FILE, 'utf-8'));
  return backlog
    .map((sujet, index) => ({ ...sujet, index }))
    .filter(sujet => sujet.statut === 'bloque');
}

module.exports = { listerBrouillons, listerBloques, ROOT, BLOG_DIR, BACKLOG_FILE };
