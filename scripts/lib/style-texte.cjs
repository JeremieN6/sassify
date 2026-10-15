// Regles d'ecriture des articles (decision de Jeremie, 2026-10-15) :
//  1. jamais de tiret comme ponctuation (cadratin, demi-cadratin, " - " entre deux idees) : une virgule a la place ;
//  2. dates et donnees chiffrees (nombres, montants, pourcentages, durees) en CHIFFRES, jamais en lettres.
// La regle 1 est APPLIQUEE par le code (le modele en remet quand meme) ; la regle 2 n'est que dans le prompt.
// Le traitement connait le markdown : il ne touche ni aux puces "- ", ni aux regles "---", ni aux tableaux,
// ni au code, ni aux mots composes avec trait d'union (vingt-deux, c'est-a-dire).

const REGLES_PROMPT = [
  'Les dates et toutes les données chiffrées (nombres, montants, pourcentages, durées, quantités) s\'écrivent TOUJOURS en chiffres (22 leads, 11 %, le 15 octobre, 3 jours, 1/3), jamais en lettres. Seuls « un/une » (article) et les expressions toutes faites restent en lettres.',
  'N\'utilise JAMAIS de tiret comme ponctuation : ni « — », ni « – », ni « - » entre deux phrases ou deux idées. Utilise une virgule, un point, deux-points ou des parenthèses ; ou rien si la phrase se lit bien sans. Les mots composés avec trait d\'union (vingt-deux, c\'est-à-dire) restent normaux.',
].join('\n');

const TIRET_LONG = /[ \t]*[—–―‒][ \t]*/g;
const TIRET_ESPACE = /[ \t]+-[ \t]+/g;

// Nettoie un morceau de prose ; ne fait le menage de virgules que si un tiret a bel et bien ete remplace.
function nettoyerProse(texte) {
  const apres = texte.replace(TIRET_LONG, ', ').replace(TIRET_ESPACE, ', ');
  if (apres === texte) return texte;
  return apres
    .replace(/,(?:[ \t]*,)+/g, ',')
    .replace(/([.!?…:;])[ \t]*,[ \t]*/g, '$1 ')
    .replace(/[ \t]*,[ \t]*([.!?…:;])/g, '$1')
    .replace(/^[ \t]*,[ \t]*/, '')
    .replace(/,[ \t]*$/, '');
}

// Une ligne : on garde sa puce ou son numero, et on ne traite que la prose hors code en ligne.
function nettoyerLigne(ligne) {
  const puce = ligne.match(/^(\s*(?:[-*+]|\d+[.)])\s+)(.*)$/);
  const prefixe = puce ? puce[1] : '';
  const reste = puce ? puce[2] : ligne;
  return prefixe + reste.split(/(`[^`]*`)/).map((morceau, i) => (i % 2 === 1 ? morceau : nettoyerProse(morceau))).join('');
}

/** Retire les tirets de ponctuation d'un texte markdown (sans abimer la structure markdown). */
function retirerTiretsPonctuation(markdown) {
  let dansCode = false;
  return String(markdown ?? '').split('\n').map((ligne) => {
    if (/^\s*(```|~~~)/.test(ligne)) { dansCode = !dansCode; return ligne; }
    if (dansCode) return ligne;
    if (/^\s*([-*_])(\s*\1){2,}\s*$/.test(ligne)) return ligne; // regle horizontale
    if (ligne.includes('|')) return ligne; // tableau
    return nettoyerLigne(ligne);
  }).join('\n');
}

module.exports = { REGLES_PROMPT, retirerTiretsPonctuation };
