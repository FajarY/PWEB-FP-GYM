/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./src/**/*.{html,js,php}", "./public/**/*.{html,js,php}"],
  theme: {
    extend: {
      fontFamily: {
        baloo: ['"Baloo Paaji 2"', "cursive"],
      },
      colors: {
        greenLight: "#CCD5AE",
        greenPale: "#E9EDC9",
        cream: "#FEFAE0",
        sand: "#FAEDCD",
        tan: "#D4A373",
        darkGray: "#5F5954",
      },
      boxShadow: {
        custom: "0px 4px 4px rgba(0, 0, 0, 0.25)", // Custom drop shadow
      },
    },
  },
  plugins: [
    function ({ addUtilities }) {
      addUtilities({
        ".text-shadow-sm": {
          "text-shadow": "0px 1px 4px rgba(0, 0, 0, 0.25)",
        },
        ".text-shadow-md": {
          "text-shadow": "0px 2px 4px rgba(0, 0, 0, 0.25)",
        },
        ".text-shadow-lg": {
          "text-shadow": "0px 4px 4px rgba(0, 0, 0, 0.25)",
        },
        ".text-shadow-none": {
          "text-shadow": "none",
        },
      });
    },
  ],
};
