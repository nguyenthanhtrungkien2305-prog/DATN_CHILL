/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ["Poppins", "sans-serif"],
                serif: ["Playfair Display", "serif"],
            },
            colors: {
                espresso: {
                    DEFAULT: "hsl(16, 23%, 19%)",
                    light: "hsl(16, 20%, 29%)",
                },
                cream: {
                    DEFAULT: "hsl(45, 100%, 94%)",
                    light: "hsl(45, 100%, 96%)",
                },
                coral: {
                    DEFAULT: "hsl(14, 82%, 65%)",
                    hover: "hsl(14, 82%, 72%)",
                },
            },
            borderRadius: {
                card: "20px",
            },
            animation: {
                "bounce-slow": "bounce 3s infinite",
                float: "float 6s ease-in-out infinite",
            },
            keyframes: {
                float: {
                    "0%, 100%": { transform: "translateY(0)" },
                    "50%": { transform: "translateY(-20px)" },
                },
            },
        },
    },
    plugins: [],
};
