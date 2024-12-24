/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
  ],
  theme: {
    extend: {
      fontFamily: {
        'sans': ['Poppins', 'Poppins Fallback'],
        'handwritten': ['Kalam', 'Kalam Fallback'],
      },
      backgroundImage: {
        'code-img': "url('../images/code.png')",
      },
    },
  },
}
