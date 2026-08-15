import type { Config } from "tailwindcss";

const config: Config = {
  content: [
    "./pages/**/*.{js,ts,jsx,tsx,mdx}",
    "./components/**/*.{js,ts,jsx,tsx,mdx}",
    "./app/**/*.{js,ts,jsx,tsx,mdx}",
  ],
  theme: {
    extend: {
      colors: {
        background: "#090d16",
        foreground: "#f8fafc",
        primary: {
          DEFAULT: "#6366f1",
          hover: "#4f46e5",
          light: "#818cf8",
          dark: "#4338ca",
        },
        accent: {
          cyan: "#00f0ff",
          purple: "#d946ef",
          emerald: "#10b981",
          amber: "#f59e0b",
        },
        card: "#111827",
        border: "#1e293b",
      },
      fontFamily: {
        sans: ['var(--font-outfit)', 'system-ui', 'sans-serif'],
      },
      boxShadow: {
        glow: "0 0 25px rgba(99, 102, 241, 0.35)",
        cyanGlow: "0 0 25px rgba(0, 240, 255, 0.35)",
        glass: "0 8px 32px 0 rgba(0, 0, 0, 0.37)",
      },
    },
  },
  plugins: [],
};
export default config;
