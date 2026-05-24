/** @type {import('tailwindcss').Config} */
export default {
  content: ["./index.html", "./src/**/*.{js,jsx}"],
  theme: {
    extend: {
      colors: {
        night: "#0a0a0a",
        midnight: "#0B0B0F",
        accent: "#0FD7D6",
        ember: "#F15A29",
        primary: "#0FD7D6",
        secondary: "#FF6B35",
        accent2: "#004E89",
        "orange-light": "#FF8A50",
        "blue-light": "#0077B6",
      },
      fontFamily: {
        sans: ['"Plus Jakarta Sans"', "sans-serif"],
      },
      boxShadow: {
        glow: "0 0 40px rgba(15, 215, 214, 0.25)",
        "card-hover": "0 8px 32px rgba(15, 215, 214, 0.1)",
        "glow-orange": "0 0 30px rgba(255, 107, 53, 0.3)",
        "glow-blue": "0 0 30px rgba(0, 78, 137, 0.3)",
        "glow-dual":
          "0 0 30px rgba(255, 107, 53, 0.15), 0 0 60px rgba(0, 78, 137, 0.1)",
        "inner-orange": "inset 0 0 30px rgba(255, 107, 53, 0.1)",
        "inner-blue": "inset 0 0 30px rgba(0, 78, 137, 0.1)",
      },
      backgroundImage: {
        "mesh-dark":
          "radial-gradient(circle at 20% 20%, rgba(255, 107, 53, 0.06), transparent 40%), radial-gradient(circle at 80% 0%, rgba(0, 78, 137, 0.08), transparent 40%), radial-gradient(circle at 50% 80%, rgba(255, 107, 53, 0.04), transparent 45%)",
        "gradient-accent": "linear-gradient(135deg, #0FD7D6, #0bc2c1)",
        "gradient-orange": "linear-gradient(135deg, #FF6B35, #FF8A50)",
        "gradient-blue": "linear-gradient(135deg, #004E89, #0077B6)",
        "gradient-dark":
          "linear-gradient(180deg, rgba(255, 107, 53, 0.08) 0%, rgba(0, 78, 137, 0.06) 100%)",
        "gradient-tri":
          "linear-gradient(135deg, #0a0a0a 0%, #FF6B35 50%, #004E89 100%)",
        "gradient-ob":
          "linear-gradient(135deg, #FF6B35, #FF8A50, #004E89, #0077B6)",
        "gradient-radial":
          "radial-gradient(ellipse at center, var(--tw-gradient-stops))",
      },
      animation: {
        "fade-in": "fadeIn 0.6s ease-out",
        "slide-up": "slideUp 0.7s ease-out",
        "slide-down": "slideDown 0.6s ease-out",
        "scale-in": "scaleIn 0.5s ease-out",
        "pulse-glow": "pulseGlow 3s ease-in-out infinite",
        float: "float 4s ease-in-out infinite",
        "spin-slow": "spin 8s linear infinite",
        shimmer: "shimmer 2s ease-in-out infinite",
      },
      keyframes: {
        fadeIn: {
          "0%": { opacity: "0", filter: "blur(4px)" },
          "100%": { opacity: "1", filter: "blur(0px)" },
        },
        slideUp: {
          "0%": { transform: "translateY(30px)", opacity: "0" },
          "100%": { transform: "translateY(0)", opacity: "1" },
        },
        slideDown: {
          "0%": { transform: "translateY(-20px)", opacity: "0" },
          "100%": { transform: "translateY(0)", opacity: "1" },
        },
        scaleIn: {
          "0%": { transform: "scale(0.95)", opacity: "0" },
          "100%": { transform: "scale(1)", opacity: "1" },
        },
        pulseGlow: {
          "0%, 100%": {
            boxShadow:
              "0 0 20px rgba(255, 107, 53, 0.2), 0 0 40px rgba(0, 78, 137, 0.1)",
          },
          "50%": {
            boxShadow:
              "0 0 40px rgba(255, 107, 53, 0.3), 0 0 60px rgba(0, 78, 137, 0.2)",
          },
        },
        float: {
          "0%, 100%": { transform: "translateY(0px)" },
          "50%": { transform: "translateY(-12px)" },
        },
        shimmer: {
          "0%": { backgroundPosition: "-200% 0" },
          "100%": { backgroundPosition: "200% 0" },
        },
      },
    },
  },
  plugins: [],
};
