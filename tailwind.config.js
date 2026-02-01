/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./public/**/*.{html,js,php}", "./app/**/*.{php,html}"],
  theme: {
    extend: {
      colors: {
        primary: '#1e63d4',
      }
    },
  },
  plugins: [],
}