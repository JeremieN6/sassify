#module.exports = {
#  apps: [
#    {
#      name: 'quizz-wedding',
#      cwd: '/srv/saas/quizz-wedding',
#      script: '.output/server/index.mjs',
#      // Node 22 charge le .env du serveur (DATABASE_URL, NUXT_ADMIN_*).
#      // Le fichier doit exister avant le premier `pm2 start`.
#      node_args: ['--env-file=/srv/saas/quizz-wedding/.env'],
#      exec_mode: 'fork',
#      instances: 1,
#      max_restarts: 10,
#      env: {
#        NODE_ENV: 'production',
#        // Nitro ecoute sur 127.0.0.1 uniquement, nginx fait le reverse proxy.
#        NITRO_HOST: '127.0.0.1',
#        NITRO_PORT: 3000,
#        PORT: 3000
#      }
#    }
#  ]
#}