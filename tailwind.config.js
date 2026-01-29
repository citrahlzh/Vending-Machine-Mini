/** @type {import('tailwindcss').Config} */

import flowbite from 'flowbite/plugin';

export default {
  content: [
    './resources/views/**/*.blade.php',
    './resources/js/**/*.js',
    './node_modules/flowbite/**/*.js',
  ],
  theme: {
    extend: {},
  },
  plugins: [flowbite],
};
