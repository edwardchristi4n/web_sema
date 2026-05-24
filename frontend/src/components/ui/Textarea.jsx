export default function Textarea({ className = "", ...props }) {
  return (
    <textarea
      className={`w-full rounded-xl border border-white/10 bg-white/[0.03] px-4 py-3 text-sm text-white placeholder-white/40 transition-all duration-200 focus:border-accent/60 focus:bg-white/[0.06] focus:outline-none focus:ring-2 focus:ring-accent/20 resize-none ${className}`}
      {...props}
    />
  );
}
