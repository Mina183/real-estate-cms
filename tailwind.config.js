import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Montserrat', 'Helvetica', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    'light': '#b1c1c7',
                    'dark': '#53666e',
                    'darker': '#252d30',
                    'accent': '#40d0cf',
                    'accent-light': '#8fc6cb',
                },
            },
        },
    },

    plugins: [forms],
};
