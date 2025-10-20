import type { Config } from 'tailwindcss';

const config: Config = {
  darkMode: 'class',
  content: ['./app/**/*.{ts,tsx}', './components/**/*.{ts,tsx}', '../../packages/ui/src/**/*.{ts,tsx}'],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Inter', 'sans-serif'],
        mono: ['"JetBrains Mono"', 'monospace'],
      },
      colors: {
        studio: {
          background: '#0F1115',
          surface: '#171A21',
          panel: '#1E232B',
          accent: '#10B981',
          highlight: '#7C3AED',
        },
      },
    },
  },
  plugins: [],
};

export default config;
