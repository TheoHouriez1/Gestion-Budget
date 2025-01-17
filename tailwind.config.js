/** @type {import('tailwindcss').Config} */
import { keepTheme } from "keep-react/keepTheme";

module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
    "./assets/react/controllers/*.jsx",
    "./assets/react/controllers/*.tsx",
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}

export default keepTheme(config);

