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
        {
          name: 'rustine-verify',
          content: 'a838c93c38d172a14109ee0296ede879',
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
    markdown: {
      // @nuxt/content ajoute rel="nofollow" a tout lien externe par defaut
      // (rehype-external-links), y compris vers les autres projets du meme
      // proprietaire (ex: stellara.sassify.fr) -- ce qui annule tout
      // interet SEO a un lien intentionnel entre projets. Une option `rel`
      // en fonction (filtrer par domaine) a ete testee mais n'a aucun effet
      // : la fonction ne survit pas au pipeline de parsing de @nuxt/content
      // (cause exacte non identifiee, probablement une frontiere de
      // serialisation). Desactive donc le plugin entierement plutot qu'une
      // regle conditionnelle qui ne marche pas. Consequence : tout lien
      // externe ajoute dans le contenu est desormais dofollow par defaut --
      // ajouter `{rel="nofollow"}` sur un lien precis (syntaxe MDC) pour les
      // cas ou on ne veut explicitement pas transmettre d'autorite SEO.
      rehypePlugins: {
        'rehype-external-links': false,
      },
    },
  },

  css: ['~/assets/css/main.css'],
})
