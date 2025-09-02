/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ['./assets/**/*.js', './templates/**/*.html.twig', './src/Blog/Infrastructure/Rendering/LeagueMarkdownConverter.php'],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Poppins', 'Poppins Fallback'],
        article: ['Inter', 'Inter Fallback'],
        handwritten: ['Kalam', 'Kalam Fallback'],
      },
      colors: {
        charade: '#282C34',
        'code-yellow': '#E6C07B',
        'code-blue': '#61AEEE',
        'code-cyan': '#56B6C2',
        'code-violet': '#C678DD',
        'code-orange': '#D19A66',
        'code-white': '#ABB2BF',
        'code-dark-gray': '#282C34',
      },
      keyframes: {
        blink: {
          to: { visibility: 'hidden' },
        },
      },
      animation: {
        blink: 'blink 2s steps(5, start) infinite',
      },
    },
  },
};
