import { Link } from "react-router-dom";
import { motion, useMotionValue, useSpring, useTransform } from "framer-motion";
import {
  Crown,
  Star,
  Heart,
  Coins,
  Radio,
  ArrowRight,
} from "lucide-react";

const divisionMeta = {
  "PENGURUS HARIAN": {
    color: "text-secondary",
    accent: "bg-secondary/10",
    border: "border-secondary/20",
    glow: "group-hover:shadow-[0_0_40px_rgba(255,107,53,0.12)]",
    hoverBorder: "group-hover:border-secondary/40",
    icon: Crown,
    gradientFrom: "from-secondary",
    gradientTo: "to-orange-300",
    desc: "Mengoordinasikan operasional SEMA dan program strategis lintas bidang.",
  },
  "MINAT BAKAT": {
    color: "text-pink-400",
    accent: "bg-pink-400/10",
    border: "border-pink-400/20",
    glow: "group-hover:shadow-[0_0_40px_rgba(244,114,182,0.12)]",
    hoverBorder: "group-hover:border-pink-400/40",
    icon: Star,
    gradientFrom: "from-pink-400",
    gradientTo: "to-rose-300",
    desc: "Pengembangan seni, musik, olahraga, dan kreativitas mahasiswa.",
  },
  "SOSIAL MASYARAKAT": {
    color: "text-emerald-400",
    accent: "bg-emerald-400/10",
    border: "border-emerald-400/20",
    glow: "group-hover:shadow-[0_0_40px_rgba(52,211,153,0.12)]",
    hoverBorder: "group-hover:border-emerald-400/40",
    icon: Heart,
    gradientFrom: "from-emerald-400",
    gradientTo: "to-green-300",
    desc: "Kegiatan sosial, lingkungan, dan pengabdian masyarakat.",
  },
  "USAHA DANA": {
    color: "text-amber-400",
    accent: "bg-amber-400/10",
    border: "border-amber-400/20",
    glow: "group-hover:shadow-[0_0_40px_rgba(251,191,36,0.12)]",
    hoverBorder: "group-hover:border-amber-400/40",
    icon: Coins,
    gradientFrom: "from-amber-400",
    gradientTo: "to-yellow-300",
    desc: "Penggalangan dana dan pengembangan program wirausaha.",
  },
  "KOMUNIKASI INFORMASI": {
    color: "text-accent2",
    accent: "bg-accent2/10",
    border: "border-accent2/20",
    glow: "group-hover:shadow-[0_0_40px_rgba(0,78,137,0.15)]",
    hoverBorder: "group-hover:border-accent2/40",
    icon: Radio,
    gradientFrom: "from-accent2",
    gradientTo: "to-blue-300",
    desc: "Pusat informasi, dokumentasi, dan media komunikasi SEMA.",
  },
};

const containerVariants = {
  hidden: {},
  visible: { transition: { staggerChildren: 0.1 } },
};

const cardVariants = {
  hidden: { opacity: 0, y: 40, filter: "blur(8px)" },
  visible: {
    opacity: 1,
    y: 0,
    filter: "blur(0px)",
    transition: { duration: 0.7, ease: [0.25, 0.1, 0.25, 1] },
  },
};

function DivisionCard({ division, meta }) {
  const IconComponent = meta.icon;

  return (
    <motion.div variants={cardVariants}>
      <Link
        to={`/bidang/${division.id_divisi}`}
        className="block h-full"
      >
        <motion.div
          whileHover={{ y: -6, scale: 1.02 }}
          transition={{ duration: 0.3, ease: "easeOut" }}
          className={`group relative flex h-full flex-col gap-5 rounded-2xl border border-white/10 bg-white/[0.03] p-7 transition-all duration-500 ${meta.glow} ${meta.hoverBorder} overflow-hidden`}
        >
          {/* Hover gradient overlay */}
          <div className={`absolute inset-0 bg-gradient-to-br ${meta.gradientFrom}/5 ${meta.gradientTo}/5 opacity-0 group-hover:opacity-100 transition-opacity duration-500 rounded-2xl`} />

          {/* Top accent line */}
          <div className={`absolute top-0 left-6 right-6 h-[2px] bg-gradient-to-r ${meta.gradientFrom} ${meta.gradientTo} scale-x-0 group-hover:scale-x-100 transition-transform duration-500 origin-left rounded-full`} />

          <div className="relative z-10">
            <motion.div
              whileHover={{ rotate: [0, -8, 8, 0], scale: 1.1 }}
              transition={{ duration: 0.4 }}
              className={`h-14 w-14 rounded-2xl ${meta.accent} ${meta.border} border flex items-center justify-center transition-all duration-300`}
            >
              <IconComponent size={24} className={meta.color} />
            </motion.div>
          </div>

          <div className="relative z-10 flex-1">
            <h3 className="text-xl font-bold tracking-tight">
              {division.nama_divisi}
            </h3>
            <p className="mt-3 text-sm text-white/50 leading-relaxed">{meta.desc}</p>
          </div>

          <div className="relative z-10">
            <span
              className={`text-sm font-semibold inline-flex items-center gap-2 ${meta.color} group-hover:text-white transition-colors duration-300`}
            >
              Lihat detail
              <motion.span
                className="inline-block"
                animate={{ x: [0, 3, 0] }}
                transition={{ duration: 1.5, repeat: Infinity, ease: "easeInOut" }}
              >
                <ArrowRight size={14} />
              </motion.span>
            </span>
          </div>
        </motion.div>
      </Link>
    </motion.div>
  );
}

export default function DivisionSection({ divisions }) {
  return (
    <section id="bidang" className="relative bg-night py-24 overflow-hidden">
      {/* Decorative elements */}
      <div className="absolute top-0 left-0 right-0 section-divider" />
      <div className="absolute -left-40 top-1/2 w-80 h-80 bg-secondary/5 rounded-full blur-[120px] pointer-events-none" />
      <div className="absolute -right-40 top-1/3 w-80 h-80 bg-accent2/5 rounded-full blur-[120px] pointer-events-none" />

      <div className="mx-auto max-w-6xl px-6 relative z-10">
        <motion.div
          initial={{ opacity: 0, y: 30 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true, margin: "-100px" }}
          transition={{ duration: 0.7 }}
          className="text-center"
        >
          <motion.p
            initial={{ opacity: 0, y: 10 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ delay: 0.1 }}
            className="inline-flex items-center gap-2 text-xs uppercase tracking-[0.4em] text-secondary font-semibold"
          >
            <span className="w-8 h-[1px] bg-secondary/50" />
            Bidang SEMA
            <span className="w-8 h-[1px] bg-secondary/50" />
          </motion.p>
          <h2 className="mt-5 text-3xl font-bold md:text-5xl">
            Lima Bidang <span className="gradient-text-orange-blue">Utama</span>
          </h2>
          <p className="mt-5 text-white/50 max-w-2xl mx-auto leading-relaxed">
            Struktur kerja yang fokus pada pelayanan, kreativitas, dan
            kontribusi untuk mahasiswa FTI.
          </p>
        </motion.div>

        <motion.div
          className="mt-14 grid gap-6 md:grid-cols-2 lg:grid-cols-3"
          variants={containerVariants}
          initial="hidden"
          whileInView="visible"
          viewport={{ once: true, margin: "-50px" }}
        >
          {divisions.map((division) => {
            const key = (division.nama_divisi || "").toUpperCase();
            const meta = divisionMeta[key] || {
              color: "text-accent",
              accent: "bg-accent/10",
              border: "border-accent/20",
              glow: "group-hover:shadow-[0_0_40px_rgba(15,215,214,0.12)]",
              hoverBorder: "group-hover:border-accent/40",
              icon: Crown,
              gradientFrom: "from-accent",
              gradientTo: "to-teal-300",
              desc: "Bidang inti yang menjaga kesinambungan program SEMA FTI UAJY.",
            };

            return (
              <DivisionCard
                key={division.id_divisi}
                division={division}
                meta={meta}
              />
            );
          })}
        </motion.div>
      </div>
    </section>
  );
}
