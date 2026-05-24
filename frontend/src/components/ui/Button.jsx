import { motion } from "framer-motion";

const sizes = {
  sm: "px-3 py-1.5 text-sm",
  md: "px-4 py-2.5 text-sm font-medium",
  lg: "px-6 py-3 text-base font-semibold",
  xl: "px-8 py-4 text-lg font-semibold",
};

const variants = {
  solid:
    "bg-accent text-night hover:bg-accent/90 active:bg-accent/80 shadow-md hover:shadow-lg hover:shadow-accent/30",
  ghost:
    "border border-white/20 text-white hover:text-white hover:border-accent/50 hover:bg-white/5",
  subtle:
    "bg-white/5 text-white/80 hover:bg-white/10 hover:text-white border border-white/5 hover:border-white/10",
  secondary:
    "bg-secondary text-white hover:bg-secondary/90 active:bg-secondary/80 shadow-md hover:shadow-lg hover:shadow-orange-500/30",
  blue: "bg-accent2 text-white hover:bg-accent2/90 active:bg-accent2/80 shadow-md hover:shadow-lg hover:shadow-blue-500/30",
  danger:
    "bg-rose-500/20 text-rose-300 border border-rose-500/30 hover:bg-rose-500/30 hover:text-rose-200",
  "gradient-orange-blue":
    "bg-gradient-to-r from-secondary to-accent2 text-white hover:opacity-90 shadow-md hover:shadow-lg",
};

export default function Button({
  children,
  size = "md",
  variant = "solid",
  className = "",
  type = "button",
  disabled = false,
  ...props
}) {
  return (
    <motion.button
      type={type}
      disabled={disabled}
      whileHover={!disabled ? { scale: 1.03 } : {}}
      whileTap={!disabled ? { scale: 0.97 } : {}}
      transition={{ duration: 0.2 }}
      className={`inline-flex items-center justify-center gap-2 rounded-xl font-medium transition-all duration-200 ease-out disabled:opacity-50 disabled:cursor-not-allowed ${sizes[size]} ${variants[variant]} ${className}`}
      {...props}
    >
      {children}
    </motion.button>
  );
}
