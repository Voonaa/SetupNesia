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
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    bg: '#0F172A',
                    primary: '#7C3AED',
                    secondary: '#3B82F6',
                    dark: '#1E293B',
                    card: '#1E293B',
                    accent: '#8B5CF6',
                }
            }
        },
    },

    plugins: [forms],
};
