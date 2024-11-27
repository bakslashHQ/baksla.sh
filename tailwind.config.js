/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
      'index.html',
  ],
  theme: {
    extend: {
      fontFamily: {
        'sans': ['Poppins'],
        'handwritten': ['Kalam'],
      },
      backgroundImage: {
        'code-img': "url('../image/code.png')",
      },
    },
  },
}
