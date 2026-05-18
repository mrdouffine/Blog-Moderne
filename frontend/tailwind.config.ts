import type { Config } from 'tailwindcss'

// =============================================================================
// Tailwind config — BlogModerne
// Reproduit exactement le système de design de la maquette
// =============================================================================
export default {
  darkMode: 'class',
  content: [
    './components/**/*.{vue,js,ts}',
    './layouts/**/*.vue',
    './pages/**/*.vue',
    './plugins/**/*.{js,ts}',
    './app.vue',
    './nuxt.config.ts',
  ],
  theme: {
    extend: {
      // ── Palette de couleurs Material You ──────────────────────────────────
      colors: {
        'on-error':                    '#ffffff',
        'on-primary':                  '#ffffff',
        'on-tertiary-fixed':           '#301400',
        'surface-tint':                '#494bd6',
        'secondary':                   '#855300',
        'primary-fixed-dim':           '#c0c1ff',
        'surface-container-highest':   '#e4e1ed',
        'error-container':             '#ffdad6',
        'on-secondary':                '#ffffff',
        'on-tertiary-fixed-variant':   '#703700',
        'primary-fixed':               '#e1e0ff',
        'on-secondary-fixed':          '#2a1700',
        'error':                       '#ba1a1a',
        'tertiary':                    '#904900',
        'inverse-surface':             '#303038',
        'surface-container-high':      '#e9e6f3',
        'on-primary-fixed-variant':    '#2f2ebe',
        'outline':                     '#767586',
        'on-primary-container':        '#fffbff',
        'secondary-container':         '#fea619',
        'tertiary-fixed-dim':          '#ffb783',
        'on-secondary-fixed-variant':  '#653e00',
        'on-surface':                  '#1b1b23',
        'background':                  '#fcf8ff',
        'surface':                     '#fcf8ff',
        'outline-variant':             '#c7c4d7',
        'on-secondary-container':      '#684000',
        'on-surface-variant':          '#464554',
        'tertiary-container':          '#b55d00',
        'on-error-container':          '#93000a',
        'on-background':               '#1b1b23',
        'on-primary-fixed':            '#07006c',
        'surface-variant':             '#e4e1ed',
        'surface-dim':                 '#dbd8e4',
        'inverse-primary':             '#c0c1ff',
        'surface-container':           '#efecf8',
        'on-tertiary-container':       '#fffbff',
        'surface-container-low':       '#f5f2fe',
        'surface-container-lowest':    '#ffffff',
        'secondary-fixed':             '#ffddb8',
        'tertiary-fixed':              '#ffdcc5',
        'primary-container':           '#6063ee',
        'inverse-on-surface':          '#f2effb',
        'on-tertiary':                 '#ffffff',
        'primary':                     '#4648d4',
        'surface-bright':              '#fcf8ff',
        'secondary-fixed-dim':         '#ffb95f',
      },

      // ── Border radius ─────────────────────────────────────────────────────
      borderRadius: {
        DEFAULT: '0.25rem',
        lg:      '0.5rem',
        xl:      '1rem',
        full:    '9999px',
      },

      // ── Espacements ───────────────────────────────────────────────────────
      spacing: {
        xs:                    '8px',
        sm:                    '16px',
        md:                    '24px',
        lg:                    '48px',
        xl:                    '80px',
        base:                  '4px',
        'container-max':       '1120px',
        'content-reading-width': '720px',
      },

      // ── Familles de polices ────────────────────────────────────────────────
      fontFamily: {
        'body-md':          ['Inter', 'sans-serif'],
        'body-lg':          ['Inter', 'sans-serif'],
        'label-sm':         ['Inter', 'sans-serif'],
        'headline-md':      ['Inter', 'sans-serif'],
        'display-lg':       ['"Playfair Display"', 'serif'],
        'display-lg-mobile':['"Playfair Display"', 'serif'],
        'code-block':       ['"JetBrains Mono"', 'monospace'],
      },

      // ── Tailles de police ─────────────────────────────────────────────────
      fontSize: {
        'body-md':    ['16px', { lineHeight: '1.6',  fontWeight: '400' }],
        'body-lg':    ['18px', { lineHeight: '1.7',  fontWeight: '400' }],
        'label-sm':   ['14px', { lineHeight: '1',    letterSpacing: '0.02em', fontWeight: '600' }],
        'headline-md':['24px', { lineHeight: '1.3',  letterSpacing: '-0.01em', fontWeight: '700' }],
        'display-lg': ['48px', { lineHeight: '1.1',  letterSpacing: '-0.02em', fontWeight: '700' }],
        'display-lg-mobile': ['36px', { lineHeight: '1.2', fontWeight: '700' }],
        'code-block': ['14px', { lineHeight: '1.5',  fontWeight: '400' }],
      },

      // ── Largeurs maximales ─────────────────────────────────────────────────
      maxWidth: {
        'container-max':        '1120px',
        'content-reading-width': '720px',
      },
    },
  },
  plugins: [],
} satisfies Config
