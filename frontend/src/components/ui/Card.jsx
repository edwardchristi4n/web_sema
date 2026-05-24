import { motion } from "framer-motion";

const colorMap = {
  accent: {
    hover: "hover:border-accent/40 hover:shadow-[0_0_30px_rgba(15,215,214,0.1)]",
    glow: "hover:bg-white/[0.06]",
  },
  orange: {
    hover: "hover:border-secondary/40 hover:shadow-[0_0_30px_rgba(255,107,53,0.1)]",
    glow: "hover:bg-white/[0.06]",
  },
  blue: {
    hover: "hover:border-accent2/40 hover:shadow-[0_0_30px_rgba(0,78,137,0.1)]",
    glow: "hover:bg-white/[0.06]",
  },
};

export default function Card({
  children,
  className = "",
  hover = false,
  accent = "accent",
}) {
  const colorStyle = colorMap[accent] || colorMap.accent;

  return (
    <motion.div
      whileHover={hover ? { y: -4, scale: 1.01 } : {}}
      transition={{ duration: 0.3, ease: "easeOut" }}
      className={`rounded-2xl border border-white/10 bg-white/[0.03] backdrop-blur-sm p-6 transition-all duration-300 ${
        hover ? `${colorStyle.hover} ${colorStyle.glow}` : ""
      } ${className}`}
    >
      {children}
    </motion.div>
  );
}
