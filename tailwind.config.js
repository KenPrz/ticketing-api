/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        // Main colors
        primary: '#8B2EFF',
        secondary: '#B15EFF',
        tertiary: '#FFA1F5',
        success: '#4CAF50',
        danger: '#FF3B30',
        warning: '#FF9500',
        info: '#7C4DFF',
        light: '#F8F9FA',
        dark: '#1C1C1E',
        
        // Gray scale
        gray: {
          100: '#F8F9FA',
          200: '#E9ECEF',
          300: '#DEE2E6',
          400: '#CED4DA',
          500: '#ADB5BD',
          600: '#6C757D',
          700: '#495057',
          800: '#343A40',
          900: '#212529',
        },
        
        // Gradient colors
        'nav': {
          from: '#450C6D',
          via: '#2A0743',
          to: '#050207',
        },
        'header': {
          from: '#6349B1',
          via: '#593894',
          to: '#4D236F',
        },
        'primary-gradient': {
          from: '#8B2EFF',
          to: '#B15EFF',
        },
        'secondary-gradient': {
          from: '#B15EFF',
          to: '#FFA1F5',
        },
      },
    },
  },
  plugins: [],
}