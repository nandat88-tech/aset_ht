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
                primary: {
                    DEFAULT: '#2563EB',
                    dark: '#1E40AF',
                    light: '#DBEAFE',
                },
                success: '#16A34A',
                warning: '#F59E0B',
                danger: '#DC2626',
                info: '#2563EB',
                sidebar: {
                    bg: '#1E293B',
                    text: '#CBD5E1',
                },
            },
            borderRadius: {
                card: '12px',
                control: '8px',
            },
            boxShadow: {
                card: '0 2px 6px rgba(0,0,0,0.06), 0 8px 24px rgba(0,0,0,0.08)',
                'card-hover': '0 4px 10px rgba(0,0,0,0.08), 0 12px 32px rgba(0,0,0,0.12)',
            },
        },
    },

    plugins: [forms],
};