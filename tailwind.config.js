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
                primary: '#546dfe',
                secondary: '#d77cf7',
                success: '#0cd7b1',
                danger: '#fe5454',
                warning: '#f4a742',
                info: '#0ca3e7',
                light: '#f5f7fa',
                dark: '#0a0a0a',
                orange: '#fe7c58',
                pink: '#fe549b',
                teal: '#00d8d8',
                purple: '#7b76fe',
                green: '#01ef8c',
                yellow: '#fff621',
            },
        },
    },

    plugins: [forms],
};
