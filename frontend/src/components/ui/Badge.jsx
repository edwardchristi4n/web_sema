const variants = {
  neutral: "bg-white/10 text-white/80 border border-white/10",
  success: "bg-emerald-500/20 text-emerald-300 border border-emerald-500/30",
  warning: "bg-amber-500/20 text-amber-300 border border-amber-500/30",
  error: "bg-rose-500/20 text-rose-300 border border-rose-500/30",
  accent: "bg-accent/20 text-accent border border-accent/30",
  orange: "bg-secondary/20 text-orange-300 border border-secondary/30",
  blue: "bg-accent2/20 text-blue-300 border border-accent2/30",
};

export default function Badge({
  children,
  variant = "neutral",
  className = "",
}) {
  return (
    <span
      className={`inline-flex items-center rounded-full px-3 py-1.5 text-xs font-medium ${variants[variant]} ${className}`}
    >
      {children}
    </span>
  );
}
