#!/usr/bin/env node
require('dotenv').config({ path: require('path').join(__dirname, '..', '.env.local') });
const fs = require('fs');
const path = require('path');
const Anthropic = require('@anthropic-ai/sdk');

const ROOT = path.join(__dirname, '..');
const BACKLOG_FILE = path.join(ROOT, 'content', 'backlog.json');
const KB_STATIC = path.join(ROOT, 'content', 'knowledge-base.md');
const KB_GENERATED = path.join(ROOT, 'content', 'knowledge-base-generated.md');
const BLOG_DIR = path.join(ROOT, 'content', 'blog');

const SYSTEM_PROMPT = `Tu écris des articles de blog pour Jérémie, un développeur solo builder qui documente publiquement la construction de son portefeuille SaaS (Sassify). Ton : direct, humble, personnel, jamais dramatisé, jamais auto-dénigrant. Structure : contexte → décision/galère → résultat concret. 400-600 mots.

Tu reçois deux sources de contexte :
- knowledge-base.md : qui est Jérémie, comment il écrit, les garde-fous stricts
- knowledge-base-generated.md : l'état réel et à jour de chaque projet

Base-toi sur knowledge-base-generated.md pour les faits précis sur le projet concerné — c'est la source la plus à jour. Respecte STRICTEMENT les garde-fous de knowledge-base.md, qui s'appliquent quel que soit le projet traité. En cas de doute sur un sujet sensible, reste plus vague plutôt que plus précis.

Si le sujet demandé ne peut pas être traité sans violer un garde-fou de knowledge-base.md — quel que soit l'angle ou la reformulation — ne rédige aucun article. Réponds uniquement par :

REFUS: une ou deux phrases expliquant précisément quel garde-fou est en cause et pourquoi aucune reformulation ne suffit.

Sinon, réponds selon ce format exact, sans rien ajouter avant ou après :

RÉSUMÉ: une phrase d'accroche (160 caractères max), distincte de la première phrase du corps — elle sert de teaser affiché séparément au-dessus de l'article, elle ne doit donc jamais répéter mot pour mot la première phrase du corps.
---
Le corps de l'article en markdown, sans frontmatter, sans titre en H1 (le titre est déjà géré séparément), sans préambule ni commentaire.`;

const TAGS = {
  process: 'Process',
  decisions: 'Décisions',
  'coulisses-techniques': 'Coulisses techniques',
};

function slugify(str) {
  return str.toLowerCase()
    .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/(^-|-$)/g, '');
}

async function main() {
  if (!process.env.ANTHROPIC_API_KEY) {
    console.error('❌ ANTHROPIC_API_KEY manquant dans .env.local');
    process.exit(1);
  }

  const backlog = JSON.parse(fs.readFileSync(BACKLOG_FILE, 'utf-8'));
  const sujetIndex = backlog.findIndex(s => s.statut === 'a_faire');

  if (sujetIndex === -1) {
    console.log('ℹ️  Aucun sujet en attente dans le backlog.');
    return;
  }

  const sujet = backlog[sujetIndex];
  console.log(`📝 Génération : "${sujet.titre}"`);

  const kbStatic = fs.readFileSync(KB_STATIC, 'utf-8');
  const kbGenerated = fs.existsSync(KB_GENERATED) ? fs.readFileSync(KB_GENERATED, 'utf-8') : '';

  const client = new Anthropic({ apiKey: process.env.ANTHROPIC_API_KEY });

  const userPrompt = `${kbStatic}\n\n---\n\n${kbGenerated}\n\n---\n\nÉcris l'article suivant :\nTitre : ${sujet.titre}\nPilier : ${sujet.pilier}\nType : ${sujet.type}\nQuestion vidéo (hookVideo) : ${sujet.hookVideo}`;

  const response = await client.messages.create({
    model: 'claude-sonnet-4-6',
    max_tokens: 2000,
    system: SYSTEM_PROMPT,
    messages: [{ role: 'user', content: userPrompt }]
  });

  const rawText = response.content.map(b => b.text || '').join('\n').trim();

  if (/^REFUS\s*:/i.test(rawText)) {
    const raison = rawText.replace(/^REFUS\s*:\s*/i, '').trim();
    console.log(`🛑 Le modèle a refusé le sujet : ${raison}`);

    backlog[sujetIndex].statut = 'bloque';
    backlog[sujetIndex].raisonBlocage = raison;
    fs.writeFileSync(BACKLOG_FILE, JSON.stringify(backlog, null, 2), 'utf-8');

    const { execSync } = require('child_process');
    execSync('git add content/backlog.json', { cwd: ROOT });
    execSync(`git commit -m "Sujet bloqué par les garde-fous : ${sujet.titre}"`, { cwd: ROOT });
    execSync('git push', { cwd: ROOT });
    console.log('✅ Backlog mis à jour (sujet bloqué) et poussé');

    const { notifierSujetBloque } = require('./telegram-notify.cjs');
    await notifierSujetBloque({ titre: sujet.titre, raison });
    console.log('✅ Notification Telegram envoyée (sujet bloqué)');

    return { bloque: true, titre: sujet.titre, raison };
  }

  const separatorIndex = rawText.indexOf('\n---\n');
  if (separatorIndex === -1) {
    throw new Error('Réponse du modèle mal formée : séparateur "---" introuvable entre le résumé et le corps.');
  }

  const description = rawText.slice(0, separatorIndex).replace(/^RÉSUMÉ:\s*/i, '').trim();
  const corps = rawText.slice(separatorIndex + 5).trim();

  const slug = slugify(sujet.titre);
  const date = new Date().toISOString().split('T')[0];
  const tag = TAGS[sujet.pilier] || sujet.pilier;

  const frontmatter = `---
title: "${sujet.titre.replace(/"/g, '\\"')}"
description: "${description.replace(/"/g, '\\"')}"
slug: ${slug}
date: ${date}
tag: ${tag}
pilier: ${sujet.pilier}
type: ${sujet.type}
hookVideo: "${sujet.hookVideo.replace(/"/g, '\\"')}"
statut: brouillon
---

`;

  fs.mkdirSync(BLOG_DIR, { recursive: true });
  const outputPath = path.join(BLOG_DIR, `${slug}.md`);
  fs.writeFileSync(outputPath, frontmatter + corps, 'utf-8');

  backlog[sujetIndex].statut = 'genere';
  fs.writeFileSync(BACKLOG_FILE, JSON.stringify(backlog, null, 2), 'utf-8');

  console.log(`✅ Article généré : ${outputPath}`);

  if (process.env.AUTO_PUBLISH === 'true') {
    const contenuActuel = fs.readFileSync(outputPath, 'utf-8');
    fs.writeFileSync(outputPath, contenuActuel.replace('statut: brouillon', 'statut: publie'), 'utf-8');
    console.log('✅ Publication automatique (AUTO_PUBLISH=true)');
    }

  const { execSync } = require('child_process');
  execSync('git add content/blog content/backlog.json', { cwd: ROOT });
  execSync(`git commit -m "Article généré : ${sujet.titre}"`, { cwd: ROOT });
  execSync('git push', { cwd: ROOT });
  console.log('✅ Commit + push effectués');

    if (process.env.AUTO_PUBLISH !== 'true') {
        const { notifierNouvelArticle } = require('./telegram-notify.cjs');
        await notifierNouvelArticle({ slug, titre: sujet.titre });
        console.log('✅ Notification Telegram envoyée');
    }

  return { slug, titre: sujet.titre, outputPath };
}

main().catch(err => {
  console.error('❌ Erreur :', err.message);
  process.exit(1);
});