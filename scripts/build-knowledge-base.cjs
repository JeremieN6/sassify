#!/usr/bin/env node
const { execSync } = require('child_process');
const fs = require('fs');
const path = require('path');
const os = require('os');

const ROOT = path.join(__dirname, '..');
const PROJECTS_CONFIG = path.join(ROOT, 'config', 'projects.json');
const WORK_DIR = path.join(os.tmpdir(), 'kb-sources');
const OUTPUT_FILE = path.join(ROOT, 'content', 'knowledge-base-generated.md');

function run(cmd, cwd) {
  execSync(cmd, { cwd, stdio: 'pipe' });
}

function main() {
  const projects = JSON.parse(fs.readFileSync(PROJECTS_CONFIG, 'utf-8'));
  fs.mkdirSync(WORK_DIR, { recursive: true });

  let output = `> Généré automatiquement le ${new Date().toISOString()} — ne jamais éditer ce fichier à la main\n\n`;

  for (const project of projects) {
    const dest = path.join(WORK_DIR, project.nom);

    try {
      if (!fs.existsSync(dest)) {
        console.log(`Clonage de ${project.nom}...`);
        run(`git clone --depth 1 ${project.repo} "${dest}"`);
      } else {
        console.log(`Mise à jour de ${project.nom}...`);
        run(`git pull`, dest);
      }

      const storyPath = path.join(dest, 'STORY.md');
      if (fs.existsSync(storyPath)) {
        const content = fs.readFileSync(storyPath, 'utf-8');
        output += `## ${project.nom}\n\n${content}\n\n---\n\n`;
        console.log(`✅ ${project.nom} : STORY.md récupéré`);
      } else {
        console.warn(`⚠️  ${project.nom} : STORY.md introuvable, ignoré`);
      }
    } catch (err) {
      const detail = err.stderr ? err.stderr.toString().trim() : err.message;
      console.error(`❌ ${project.nom} : ${detail}`);
    }
  }

  fs.mkdirSync(path.dirname(OUTPUT_FILE), { recursive: true });
  fs.writeFileSync(OUTPUT_FILE, output, 'utf-8');
  console.log(`\n✅ Terminé : ${OUTPUT_FILE} (${output.length} caractères)`);
}

main();