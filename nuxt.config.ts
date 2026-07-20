export default defineNuxtConfig({
  compatibilityDate: '2024-11-01',
  devtools: { enabled: true },

  app: {
    head: {
      meta: [
        {
          name: 'google-site-verification',
          content: 'dS4lDtb3GkUFSthFb5DQkzfwTUYCP_dKFWE5m1s7V8E',
        },
      ],
    },
  },

  modules: [
    '@nuxt/content',
    '@nuxtjs/tailwindcss',
    '@nuxtjs/google-fonts',
  ],

  googleFonts: {
    families: {
      Syne: [400, 600, 700, 800],
      Inter: [400, 500, 600],
    },
    display: 'swap',
    download: true,
  },

  content: {
    highlight: {
      theme: 'github-dark',
    },
  },

  css: ['~/assets/css/main.css'],
})
