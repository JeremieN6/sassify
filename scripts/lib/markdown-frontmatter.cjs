const fs = require('fs');

// Aucune dependance yaml n'est installee dans ce projet, et le frontmatter des
// articles ne contient que des paires cle: valeur a plat (jamais de listes ni
// d'objets imbriques) -- un parsing ligne a ligne suffit et evite d'ajouter une
// dependance pour si peu. Suffisant pour relire exactement ce que
// generate-article.cjs ecrit lui-meme.
function parseFrontmatterValue(raw) {
  const trimmed = raw.trim();
  const quoted = trimmed.match(/^"(.*)"$/s);
  if (quoted) {
    return quoted[1].replace(/\\"/g, '"');
  }
  return trimmed;
}

function parseFrontmatter(raw) {
  const frontmatter = {};
  for (const line of raw.split('\n')) {
    const match = line.match(/^([a-zA-Z0-9_]+):\s*(.*)$/);
    if (!match) continue;
    frontmatter[match[1]] = parseFrontmatterValue(match[2]);
  }
  return frontmatter;
}

/**
 * Lit un fichier markdown avec frontmatter (---\n...\n---\n corps).
 * Retourne { frontmatter, body }. Leve une erreur claire si le fichier
 * n'existe pas ou si le frontmatter est absent/mal forme.
 */
function readMarkdownWithFrontmatter(filePath) {
  if (!fs.existsSync(filePath)) {
    throw new Error(`Fichier introuvable : ${filePath}`);
  }

  // Certains articles sont enregistres en CRLF (edites sous Windows): on
  // normalise avant de parser, sinon les \n litteraux du regex ne matchent pas.
  const raw = fs.readFileSync(filePath, 'utf-8').replace(/\r\n/g, '\n');
  const match = raw.match(/^---\n([\s\S]*?)\n---\n?([\s\S]*)$/);
  if (!match) {
    throw new Error(`Frontmatter absent ou mal forme dans : ${filePath}`);
  }

  return {
    frontmatter: parseFrontmatter(match[1]),
    body: match[2].trim(),
  };
}

module.exports = { readMarkdownWithFrontmatter };
