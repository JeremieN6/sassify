const crypto = require('crypto');

// Telegram limite callback_data à 64 octets. Les slugs d'articles peuvent
// être plus longs que ça, donc on utilise un id court dérivé du slug pour
// les boutons, et on retrouve le fichier correspondant en recalculant ce
// même id pour chaque article présent sur le disque.
function shortId(slug) {
  return crypto.createHash('sha1').update(slug).digest('hex').slice(0, 12);
}

module.exports = { shortId };
