/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ['./assets/**/*.js', './templates/**/*.html.twig', './src/Blog/Infrastructure/Rendering/LeagueMarkdownConverter.php'],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Space Grotesk', 'Space Grotesk Fallback', 'ui-sans-serif', 'system-ui', 'sans-serif'],
        mono: ['JetBrains Mono', 'JetBrains Mono Fallback', 'ui-monospace', 'SFMono-Regular', 'Menlo', 'monospace'],
        poppins: ['Poppins', 'Poppins Fallback'],
        article: ['Inter', 'Inter Fallback'],
      },
      colors: {
        accent: '#860DFF',
        paper: '#F6F2E8',
        ink: '#1F2130',
        'ink-2': '#3B3E4A',
        muted: '#5E646C',

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
        'word-in': {
          from: { opacity: '0', transform: 'translateY(10px) scale(0.95) rotate(var(--tilt, 0deg))' },
          to: { opacity: '1', transform: 'translateY(0) scale(1) rotate(var(--tilt, 0deg))' },
        },
      },
      animation: {
        blink: 'blink 2s steps(5, start) infinite',
        'word-in': 'word-in 420ms cubic-bezier(.2,.8,.2,1) forwards',
      },
    },
  },
};
