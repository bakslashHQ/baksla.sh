/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
      'index.html',
  ],
  theme: {
    extend: {
      fontFamily: {
        'sans': ['Poppins', 'Poppins Fallback'],
        'handwritten': ['Kalam', 'Kalam Fallback'],
      },
      backgroundImage: {
        'code-img': "url('../image/code.png')",
      },
    },
  },
}
