import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Poppins', 'Figtree', ...defaultTheme.fontFamily.sans],
                satoshi: ['Satoshi', 'sans-serif'],
                neue: ['Neue Montreal', 'sans-serif'],
            },
            colors: {
                admin: {
                    sidebar: '#0F0F10',
                    main: '#09090B',
                    accent: '#C7A27C', // Luxury accent
                    modern: '#9FE870', // Modern sneaker accent
                }
            }
        },
    },

    plugins: [forms],
};
