import { motion } from "framer-motion";
import {
  Heart,
  Music,
  Zap,
  Camera,
  Award,
  Plus,
  ArrowUpRight,
} from "lucide-react";

const communities = [
  {
    name: "Badminton",
    desc: "Komunitas bulu tangkis aktif berlatih dan kompetitif.",
    tag: "Olahraga",
    icon: Heart,
    gradient: "from-secondary to-orange-300",
    dotColor: "bg-secondary",
  },
  {
    name: "SIKMA",
    desc: "Komunitas seni dan musik dengan fokus panggung.",
    tag: "Seni & Budaya",
    icon: Music,
    gradient: "from-accent2 to-blue-300",
    dotColor: "bg-accent2",
  },
  {
    name: "Texere",
    desc: "Tim basket FTI bersaing di turnamen kampus.",
    tag: "Olahraga",
    icon: Zap,
    gradient: "from-accent to-teal-300",
    dotColor: "bg-accent",
  },
  {
    name: "FTI Image",
    desc: "Fotografi dan videografi untuk dokumentasi.",
    tag: "Visual",
    icon: Camera,
    gradient: "from-secondary to-rose-300",
    dotColor: "bg-secondary",
  },
  {
    name: "Futsal",
    desc: "Komunitas futsal rutin latihan dan tanding.",
    tag: "Olahraga",
    icon: Award,
    gradient: "from-emerald-400 to-green-300",
    dotColor: "bg-emerald-400",
  },
  {
    name: "Komunitas Lainnya",
    desc: "Hubungi kami untuk info komunitas lain.",
    tag: "Info",
    icon: Plus,
    gradient: "from-accent2 to-accent",
    dotColor: "bg-accent2",
  },
];

const containerVariants = {
  hidden: {},
  visible: { transition: { staggerChildren: 0.08 } },
};

const cardVariants = {
  hidden: { opacity: 0, y: 30, scale: 0.95 },
  visible: {
    opacity: 1,
    y: 0,
    scale: 1,
    transition: { duration: 0.6, ease: [0.25, 0.1, 0.25, 1] },
  },
};

export default function CommunitySection() {
  return (
    <section
      id="komunitas"
      className="relative bg-midnight py-24 overflow-hidden"
    >
      {/* Decorative */}
      <div className="absolute top-0 left-0 right-0 section-divider" />
      <div className="absolute top-1/2 -translate-y-1/2 left-0 w-[600px] h-[600px] bg-gradient-radial from-accent2/5 to-transparent rounded-full blur-[100px] pointer-events-none" />
      <div className="absolute top-1/3 right-0 w-[400px] h-[400px] bg-gradient-radial from-secondary/5 to-transparent rounded-full blur-[100px] pointer-events-none" />

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
            className="inline-flex items-center gap-2 text-xs uppercase tracking-[0.4em] text-accent2 font-semibold"
          >
            <span className="w-8 h-[1px] bg-accent2/50" />
            Komunitas
            <span className="w-8 h-[1px] bg-accent2/50" />
          </motion.p>
          <h2 className="mt-5 text-3xl font-bold md:text-5xl">
            Komunitas Mahasiswa <span className="gradient-text-ocean">FTI</span>
          </h2>
          <p className="mt-5 text-white/50 max-w-xl mx-auto">
            Temukan komunitas yang sesuai minat dan kembangkan potensi bersama.
          </p>
        </motion.div>

        <motion.div
          className="mt-14 grid gap-5 md:grid-cols-2 lg:grid-cols-3"
          variants={containerVariants}
          initial="hidden"
          whileInView="visible"
          viewport={{ once: true, margin: "-50px" }}
        >
          {communities.map((item) => {
            const IconComponent = item.icon;
            return (
              <motion.div
                key={item.name}
                variants={cardVariants}
                whileHover={{ y: -6, scale: 1.02 }}
                transition={{ duration: 0.3 }}
                className="group relative rounded-2xl border border-white/[0.08] bg-white/[0.02] p-6 transition-all duration-500 hover:border-white/20 hover:bg-white/[0.05] overflow-hidden cursor-pointer"
              >
                {/* Hover gradient bg */}
                <div
                  className={`absolute inset-0 bg-gradient-to-br ${item.gradient} opacity-0 group-hover:opacity-[0.04] transition-opacity duration-500 rounded-2xl`}
                />

                <div className="relative z-10">
                  <div className="flex items-center justify-between mb-4">
                    <motion.div
                      whileHover={{ rotate: 12, scale: 1.15 }}
                      transition={{ duration: 0.3 }}
                      className={`h-12 w-12 rounded-2xl bg-gradient-to-br ${item.gradient} bg-opacity-20 flex items-center justify-center shadow-lg`}
                      style={{
                        background: `linear-gradient(135deg, rgba(255,107,53,0.1), rgba(0,78,137,0.1))`,
                      }}
                    >
                      <IconComponent size={22} className="text-white/80" />
                    </motion.div>
                    <motion.div
                      initial={{ opacity: 0, x: -10 }}
                      whileHover={{ opacity: 1, x: 0 }}
                      className="opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                    >
                      <ArrowUpRight size={18} className="text-white/40" />
                    </motion.div>
                  </div>

                  <h3 className="text-lg font-bold">{item.name}</h3>
                  <p className="text-sm text-white/45 mt-2 leading-relaxed">
                    {item.desc}
                  </p>

                  <div className="mt-5 flex items-center gap-2">
                    <span
                      className={`w-1.5 h-1.5 rounded-full ${item.dotColor}`}
                    />
                    <span className="text-xs text-white/40 uppercase tracking-[0.2em] font-medium">
                      {item.tag}
                    </span>
                  </div>
                </div>
              </motion.div>
            );
          })}
        </motion.div>
      </div>
    </section>
  );
}
