export default defineNuxtConfig({
  compatibilityDate: '2024-11-01',
  devtools: { enabled: true },

  site: {
    url: 'https://sassify.fr',
  },

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
    '@nuxtjs/sitemap',
    '@nuxt/content',
    '@nuxtjs/tailwindcss',
    '@nuxtjs/google-fonts',
  ],

  routeRules: {
    '/admin/**': {
      robots: false,
    },
  },

  sitemap: {
    sources: ['/api/__sitemap__/urls'],
    exclude: ['/admin/**'],
  },

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
