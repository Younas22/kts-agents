/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ['./*.php', './views/**/*.php', './includes/helpers.php', './assets/js/**/*.js'],
  theme: {
    extend: {
      colors: {
        brand: {
          50: '#EEF6FC',
          100: '#D7EAF7',
          200: '#AFD4EE',
          300: '#7DB8E2',
          400: '#3D95D0',
          500: '#0077BE',
          600: '#006AAA',
          700: '#005A91',
          800: '#084A75',
          900: '#0C3A5A',
        },
        ink: {
          DEFAULT: '#0E1C2B',
          soft: '#334155',
          muted: '#5B6B7F',
        },
        line: '#E3E8EF',
        surface: '#F6F8FB',
      },
      fontFamily: {
        sans: ['"Inter Variable"', 'Inter', 'ui-sans-serif', 'system-ui', '-apple-system', 'Segoe UI', 'Roboto', 'Helvetica Neue', 'Arial', 'sans-serif'],
      },
      boxShadow: {
        card: '0 1px 2px rgba(14, 28, 43, 0.04), 0 4px 16px -4px rgba(14, 28, 43, 0.08)',
        lift: '0 2px 4px rgba(14, 28, 43, 0.04), 0 18px 40px -12px rgba(14, 28, 43, 0.18)',
        focus: '0 0 0 4px rgba(0, 119, 190, 0.18)',
      },
      maxWidth: {
        page: '76rem',
      },
    },
  },
  plugins: [],
};
