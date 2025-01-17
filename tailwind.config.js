// ./tailwindcss -i public/css/tailwind-input.css -o public/css/tailwind-output.css --watch
const plugin = require('tailwindcss/plugin')
module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    screens: {
      '4xl': { 'max': '1691px' },// => @media (max-width: 1673px) { ... }
      '3xl': { 'max': '1550px' },// => @media (max-width: 1250px) { ... }
      '2xl': { 'max': '1280px' },// => @media (max-width: 1250px) { ... }
      'xl': { 'max': '1199px' },// => @media (max-width: 1199px) { ... }
      'lg': { 'max': '992px' },// => @media (max-width: 992px) { ... }
      'md': { 'max': '767px' },// => @media (max-width: 767px) { ... }
      'sm': { 'max': '479px' },// => @media (max-width: 479px) { ... }
      'xsm': { 'max': '389px' },// => @media (max-width: 389px) { ... }
      '2xsm': { 'max': '320px' },// => @media (max-width: 319px) { ... }
    },
    extend: {
      lineHeight: {
        3: "1.2rem",
        4: "1.6rem",
        5: "2.0rem",
        6: "2.4rem",
        7: "2.8rem",
        8: "3.2rem",
        9: "3.6rem",
        10: "4.0rem",
      },
      fontSize: {
        xs: ["1.2rem", { lineHeight: "1.6rem" }],
        sm: ["1.4rem", { lineHeight: "2.0rem" }],
        base: ["1.6rem", { lineHeight: "2.4rem" }],
        lg: ["1.8rem", { lineHeight: "2.8rem" }],
        xl: ["2.0rem", { lineHeight: "2.8rem" }],
        "2xl": ["2.4rem", { lineHeight: "3.2rem" }],
        "3xl": ["3.0rem", { lineHeight: "3.6rem" }],
        "4xl": ["3.6rem", { lineHeight: "4.0rem" }],
        "5xl": ["4.8rem", { lineHeight: "1" }],
        "6xl": ["6.0rem", { lineHeight: "1" }],
        "7xl": ["7.2rem", { lineHeight: "1" }],
        "8xl": ["9.6rem", { lineHeight: "1" }],
        "9xl": ["12.8rem", { lineHeight: "1" }],
      },
      spacing: {
        px: ".1rem",
        0.5: ".2rem",
        1: ".4rem",
        1.5: ".6rem",
        2: ".8rem",
        2.5: "1.0rem",
        3: "1.2rem",
        3.5: "1.4rem",
        4: "1.6rem",
        5: "2.0rem",
        6: "2.4rem",
        7: "2.8rem",
        8: "3.2rem",
        9: "3.6rem",
        10: "4.0rem",
        11: "4.4rem",
        12: "4.8rem",
        13.5: "5.4rem",
        14: "5.6rem",
        16: "6.4rem",
        20: "8.0rem",
        24: "9.6rem",
        28: "11.2rem",
        32: "12.8rem",
        36: "14.4rem",
        40: "16.0rem",
        44: "17.6rem",
        48: "19.2rem",
        52: "20.8rem",
        56: "22.4rem",
        60: "24.0rem",
        64: "25.6rem",
        72: "28.8rem",
        80: "32.0rem",
        96: "38.4rem",
        34: "8.5rem",
        68: "27.2rem",
        82.5: "33.0rem",
        90: "36.0rem",
        100: "40.0rem",
        106: "42.4rem",
        200: "80.0rem",
      },
      borderRadius: {
        sm: ".2rem",

      },
      minWidth: (theme) => ({
        ...theme("spacing"),
      }),
      maxWidth: (theme) => ({
        ...theme("spacing"),
        0: "0rem",
        xs: "32.0rem",
        sm: "38.4rem",
        md: "44.8rem",
        lg: "51.2rem",
        xl: "57.6rem",
        "2xl": "67.2rem",
        "3xl": "76.8rem",
        "4xl": "89.6rem",
        "5xl": "102.4rem",
        "6xl": "115.2rem",
        "7xl": "128.0rem",
        "1/2": "50%",
        "4/5": "80%",
        "1330": "1330px",

      }),
      content: {
        'arrowleftIcon': 'url("../images/arrow_left_icon.svg")',
        'arrowrightIcon': 'url("../images/arrow_right_icon.svg")',
      },
      fontFamily: {
        fontSpaceGrotesk: ['Space Grotesk', 'sans-serif'],
      },
      width: {
        "w10": "10%",
        "w30": "30%",
        "w40": "40%",
        "w85": "85%",
        "w15": "15%",
      },
      inset: {
        "10": "10%",
        "20": "20%",
        "16": "16%",
      },
      colors: {
        white: "#ffffff",
        black: "#000000",
        black800: "#241A00",
        purple: "#700D4A",
        whitesmoke400: '#F6F5F5',
        siteYellow: '#F3CD0E',
        siteYellow400: '#F9F1CB',
        siteYellow800: '#EF6A00',
        textGray: '#848484',
        textGray400: '#F6F6F6',
        textGray500: '#737373',
        black500: "#898376",
      },
    },

    container: {
      center: true,
      padding: '2rem',
      screens: {
        '2xl': '1280px',
        'xl': '1280px',
      },
    },
    boxShadow: {
      'newsBox': '0px 4px 44px 0px rgba(19, 71, 104, 0.08)',
    },
    dropShadow: {
      'text_hover': [
        '0 0 .01px #292929'
      ]
    },
    backgroundImage: {
      'waysGradientBgBottomToTop': "linear-gradient(0deg, rgba(29, 161, 243, 0.12) 0%, rgba(29, 161, 243, 0.00) 100%)",
      'calenderIcon': 'url("images/calender.svg")',
    },
  },
  plugins: [
    // plugin(function({ addVariant }) {
    //         addVariant('current', '&.active');
    //     })
  ],
  mode: "jit",
};