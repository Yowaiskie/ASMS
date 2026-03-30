/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./public/**/*.{html,js,php}", "./app/**/*.{php,html}"],
  theme: {
    extend: {
      colors: {
        primary: {
          50: '#fdf4f4',
          100: '#fae8e7',
          200: '#f6d2d1',
          300: '#f0b1af',
          400: '#e48380',
          500: '#d45652',
          DEFAULT: '#a33b39',
          600: '#a33b39',
          700: '#892f2d',
          800: '#722a28',
          900: '#612625',
        },
        secondary: {
          DEFAULT: '#00599c',
          50: '#f0f7ff',
          100: '#e0effe',
          500: '#00599c',
          600: '#004a82',
        },
        accent: {
          DEFAULT: '#f9c402',
          50: '#fffbeb',
          500: '#f9c402',
          600: '#eab308',
        }
      }
    },
  },
  plugins: [],
}