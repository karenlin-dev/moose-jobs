module.exports = {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
  ],
   safelist: [
    'grid-cols-1', 'grid-cols-2', 'grid-cols-3', 'grid-cols-4',
    'gap-0','gap-1','gap-2','gap-3','gap-4','gap-5','gap-6'
  ],
  theme: {
    extend: {},
  },
  plugins: [require('@tailwindcss/forms')],
}
