import typography from '@tailwindcss/typography'
import type { Config } from 'tailwindcss'

export default {
  content: [
    './components/**/*.{js,vue,ts}',
    './layouts/**/*.vue',
    './pages/**/*.vue',
    './plugins/**/*.{js,ts}',
    './app.vue',
  ],
  theme: {
    extend: {
      fontFamily: {
        display: ['Syne', 'sans-serif'],
        body: ['Inter', 'sans-serif'],
      },
      colors: {
        accent: {
          DEFAULT: '#6366F1',
          hover: '#4F52E0',
        },
        surface: {
          DEFAULT: '#111118',
          2: '#1A1A24',
          3: '#252535',
        },
        divider: '#2A2A38',
      },
    },
  },
  plugins: [typography],
} satisfies Config
