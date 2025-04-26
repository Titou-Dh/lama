tailwind.config = {
  theme: {
    extend: {
      colors: {
        primary: {
          blue: "#3b82f6",
          purple: "#8b5cf6",
          dark: "#1e40af",
          light: "#93c5fd",
          burgundy: "#9f1239",
        },
        secondary: {
          purple: "#7c3aed",
          light: "#c4b5fd",
        },
      },
      fontFamily: {
        poppins: ["Poppins", "sans-serif"],
      },
      screens: {
        sm: "640px",
        md: "768px",
        lg: "1024px",
        xl: "1280px",
        "2xl": "1536px",
      },
    },
  },
};
