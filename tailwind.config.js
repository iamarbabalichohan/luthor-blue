/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./*.php", "./**/*.php", "./assets/css/*.css", "./assets/js/*.js"],
  theme: {
    extend: {
      colors: {
        dark: "#333333",
      },
    },
  },
  plugins: [],
};
