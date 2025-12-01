import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            // NatuurGame Color Palette
            colors: {
                'forest': {
                    DEFAULT: '#2E7D32',
                    50: '#E8F5E9',
                    100: '#C8E6C9',
                    200: '#A5D6A7',
                    300: '#81C784',
                    400: '#66BB6A',
                    500: '#4CAF50',
                    600: '#43A047',
                    700: '#388E3C',
                    800: '#2E7D32',
                    900: '#1B5E20',
                },
                'action': {
                    DEFAULT: '#FF6B35',
                    50: '#FFF3EE',
                    100: '#FFE4D9',
                    200: '#FFC9B3',
                    300: '#FFAE8D',
                    400: '#FF8C61',
                    500: '#FF6B35',
                    600: '#E55A2B',
                    700: '#CC4A21',
                    800: '#B33A17',
                    900: '#992A0D',
                },
                'sky': {
                    DEFAULT: '#0076A8',
                    50: '#E6F4F9',
                    100: '#B3DFF0',
                    200: '#80CAE7',
                    300: '#4DB5DE',
                    400: '#1AA0D5',
                    500: '#0076A8',
                    600: '#006691',
                    700: '#00567A',
                    800: '#004663',
                    900: '#00364C',
                },
                'pure-white': '#FFFFFF',
                'deep-black': '#1B1B1B',
                'surface': {
                    light: '#F5F5F5',
                    medium: '#E0E0E0',
                },
            },

            // Typography - Lexend (dyslexia-friendly)
            fontFamily: {
                sans: ['Lexend', ...defaultTheme.fontFamily.sans],
            },

            // Custom font sizes with responsive scaling
            fontSize: {
                'h1': ['clamp(2rem, 5vw, 3rem)', { lineHeight: '1.2', fontWeight: '700' }],
                'h2': ['clamp(1.5rem, 4vw, 2rem)', { lineHeight: '1.3', fontWeight: '700' }],
                'h3': ['1.25rem', { lineHeight: '1.4', fontWeight: '500' }],
                'body': ['1rem', { lineHeight: '1.8', fontWeight: '400' }],
                'small': ['0.875rem', { lineHeight: '1.6', fontWeight: '400' }],
            },

            // Border radius tokens
            borderRadius: {
                'button': '10px',
                'card': '8px',
                'input': '6px',
                'icon': '16px',
                'badge': '12px',
            },

            // Layout
            maxWidth: {
                'container': '1200px',
            },

            // Shadows
            boxShadow: {
                'card': '0 2px 8px rgba(0,0,0,0.1)',
                'card-hover': '0 8px 24px rgba(0,0,0,0.15)',
            },
        },
    },

    plugins: [forms],
};
