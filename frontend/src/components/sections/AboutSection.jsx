import { motion, useInView } from "framer-motion";
import { useRef } from "react";
import { Eye, Target, Users, Handshake, Lightbulb, TrendingUp, Flame, Waves } from "lucide-react";
import Card from "../ui/Card.jsx";

const stats = [
  { label: "Bidang Aktif", value: "5", icon: Users, gradient: "from-secondary to-orange-300" },
  { label: "Komunitas", value: "12+", icon: Handshake, gradient: "from-accent2 to-blue-300" },
  { label: "Program Kerja", value: "30+", icon: Lightbulb, gradient: "from-secondary to-accent2" },
  { label: "Kolaborasi", value: "100+", icon: TrendingUp, gradient: "from-accent to-teal-300" },
];

const containerVariants = {
  hidden: {},
  visible: { transition: { staggerChildren: 0.1, delayChildren: 0.2 } },
};

const itemVariants = {
  hidden: { opacity: 0, y: 25, filter: "blur(4px)" },
  visible: {
    opacity: 1,
    y: 0,
    filter: "blur(0px)",
    transition: { duration: 0.6, ease: [0.25, 0.1, 0.25, 1] },
  },
};

function AnimatedCounter({ value, delay = 0 }) {
  const ref = useRef(null);
  const isInView = useInView(ref, { once: true });

  return (
    <motion.span
      ref={ref}
      initial={{ opacity: 0, scale: 0.5 }}
      animate={isInView ? { opacity: 1, scale: 1 } : {}}
      transition={{ duration: 0.5, delay, type: "spring", stiffness: 150 }}
      className="inline-block"
    >
      {value}
    </motion.span>
  );
}

export default function AboutSection() {
  return (
    <section id="tentang" className="relative bg-midnight py-24 overflow-hidden">
      {/* Decorative */}
      <div className="absolute top-0 left-0 right-0 section-divider" />
      <div className="absolute top-20 -left-40 w-[500px] h-[500px] bg-secondary/5 rounded-full blur-[120px] pointer-events-none" />
      <div className="absolute bottom-20 -right-40 w-[500px] h-[500px] bg-accent2/5 rounded-full blur-[120px] pointer-events-none" />

      <div className="mx-auto max-w-6xl px-6 relative z-10">
        <div className="grid gap-14 lg:grid-cols-2">
          {/* Left column */}
          <motion.div
            initial={{ opacity: 0, x: -40 }}
            whileInView={{ opacity: 1, x: 0 }}
            viewport={{ once: true, margin: "-100px" }}
            transition={{ duration: 0.8, ease: [0.25, 0.1, 0.25, 1] }}
          >
            <motion.p
              initial={{ opacity: 0, y: 10 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ delay: 0.1 }}
              className="inline-flex items-center gap-2 text-xs uppercase tracking-[0.4em] text-secondary font-semibold"
            >
              <span className="w-8 h-[1px] bg-secondary/50" />
              Tentang SEMA
            </motion.p>
            <h2 className="mt-5 text-3xl font-bold md:text-5xl leading-tight">
              Merajut aspirasi,{" "}
              <span className="gradient-text-orange-blue">membangun komunitas.</span>
            </h2>
            <p className="mt-6 text-white/50 leading-relaxed">
              SEMA FTI UAJY hadir sebagai lembaga kemahasiswaan yang
              kolaboratif, adaptif, dan berorientasi pada pengembangan potensi.
              Kami fokus pada ruang aspirasi, kreativitas, dan jejaring lintas
              bidang.
            </p>

            {/* Tags */}
            <div className="mt-8 flex flex-wrap gap-3">
              {[
                { tag: "Solidaritas", color: "secondary" },
                { tag: "Potensi", color: "accent2" },
                { tag: "Sosial", color: "secondary" },
                { tag: "Kreativitas", color: "accent2" },
              ].map((item, idx) => (
                <motion.span
                  key={item.tag}
                  initial={{ opacity: 0, scale: 0.8, y: 10 }}
                  whileInView={{ opacity: 1, scale: 1, y: 0 }}
                  viewport={{ once: true }}
                  transition={{ delay: 0.3 + idx * 0.08, type: "spring", stiffness: 200 }}
                  whileHover={{ scale: 1.08, y: -2 }}
                  className={`px-4 py-2 rounded-xl text-xs uppercase tracking-[0.25em] border cursor-default transition-all duration-300 ${
                    item.color === "secondary"
                      ? "bg-secondary/10 text-secondary border-secondary/20 hover:bg-secondary/20 hover:border-secondary/40"
                      : "bg-accent2/10 text-accent2 border-accent2/20 hover:bg-accent2/20 hover:border-accent2/40"
                  }`}
                >
                  {item.tag}
                </motion.span>
              ))}
            </div>

            {/* Stats grid */}
            <motion.div
              className="mt-10 grid grid-cols-2 gap-4"
              variants={containerVariants}
              initial="hidden"
              whileInView="visible"
              viewport={{ once: true, margin: "-50px" }}
            >
              {stats.map((item, idx) => {
                const IconComponent = item.icon;
                return (
                  <motion.div key={item.label} variants={itemVariants}>
                    <motion.div
                      whileHover={{ y: -4, scale: 1.03 }}
                      transition={{ duration: 0.25 }}
                      className="group rounded-2xl border border-white/[0.08] bg-white/[0.02] p-5 transition-all duration-300 hover:border-white/20 hover:bg-white/[0.05]"
                    >
                      <div className="flex items-center gap-3">
                        <div className={`flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br ${item.gradient} transition-transform group-hover:scale-110`}>
                          <IconComponent size={18} className="text-white" />
                        </div>
                        <div>
                          <span className={`text-2xl font-extrabold bg-gradient-to-r ${item.gradient} bg-clip-text text-transparent`}>
                            <AnimatedCounter value={item.value} delay={idx * 0.1} />
                          </span>
                          <p className="text-[10px] uppercase tracking-[0.25em] text-white/40 mt-0.5">
                            {item.label}
                          </p>
                        </div>
                      </div>
                    </motion.div>
                  </motion.div>
                );
              })}
            </motion.div>
          </motion.div>

          {/* Right column - Visi & Misi */}
          <motion.div
            className="space-y-6"
            initial={{ opacity: 0, x: 40 }}
            whileInView={{ opacity: 1, x: 0 }}
            viewport={{ once: true, margin: "-100px" }}
            transition={{ duration: 0.8, delay: 0.2, ease: [0.25, 0.1, 0.25, 1] }}
          >
            {/* Visi Card */}
            <motion.div
              whileHover={{ y: -4 }}
              transition={{ duration: 0.3 }}
              className="group relative rounded-2xl border border-white/10 bg-white/[0.03] p-7 overflow-hidden transition-all duration-500 hover:border-accent2/30 hover:shadow-[0_0_40px_rgba(0,78,137,0.08)]"
            >
              {/* Accent gradient line */}
              <div className="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-accent2 to-blue-300 rounded-l-2xl" />
              <div className="absolute inset-0 bg-gradient-to-r from-accent2/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500" />

              <div className="relative z-10">
                <div className="flex items-center gap-3 mb-4">
                  <motion.div
                    whileHover={{ rotate: 12 }}
                    className="h-11 w-11 rounded-xl bg-gradient-to-br from-accent2 to-blue-400 flex items-center justify-center shadow-lg shadow-accent2/20"
                  >
                    <Eye size={20} className="text-white" />
                  </motion.div>
                  <div>
                    <h3 className="text-sm font-bold uppercase tracking-[0.3em] text-accent2">
                      Visi
                    </h3>
                    <div className="w-12 h-[2px] bg-gradient-to-r from-accent2 to-transparent rounded-full mt-1" />
                  </div>
                </div>
                <p className="text-sm text-white/55 leading-relaxed pl-1">
                  Mewujudkan SEMA FTI UAJY sebagai wadah pengembangan potensi
                  serta menumbuhkan rasa empati dan simpati antar mahasiswa FTI
                  UAJY.
                </p>
              </div>
            </motion.div>

            {/* Misi Card */}
            <motion.div
              whileHover={{ y: -4 }}
              transition={{ duration: 0.3 }}
              className="group relative rounded-2xl border border-white/10 bg-white/[0.03] p-7 overflow-hidden transition-all duration-500 hover:border-secondary/30 hover:shadow-[0_0_40px_rgba(255,107,53,0.08)]"
            >
              {/* Accent gradient line */}
              <div className="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-secondary to-orange-300 rounded-l-2xl" />
              <div className="absolute inset-0 bg-gradient-to-r from-secondary/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500" />

              <div className="relative z-10">
                <div className="flex items-center gap-3 mb-5">
                  <motion.div
                    whileHover={{ rotate: 12 }}
                    className="h-11 w-11 rounded-xl bg-gradient-to-br from-secondary to-orange-300 flex items-center justify-center shadow-lg shadow-secondary/20"
                  >
                    <Target size={20} className="text-white" />
                  </motion.div>
                  <div>
                    <h3 className="text-sm font-bold uppercase tracking-[0.3em] text-secondary">
                      Misi
                    </h3>
                    <div className="w-12 h-[2px] bg-gradient-to-r from-secondary to-transparent rounded-full mt-1" />
                  </div>
                </div>
                <ul className="space-y-4">
                  {[
                    "Menyediakan wadah pengembangan potensi non-akademik.",
                    "Meningkatkan komunikasi dan solidaritas mahasiswa.",
                    "Mendorong partisipasi aktif dalam kegiatan SEMA.",
                  ].map((item, idx) => (
                    <motion.li
                      key={idx}
                      initial={{ opacity: 0, x: -15 }}
                      whileInView={{ opacity: 1, x: 0 }}
                      viewport={{ once: true }}
                      transition={{ delay: 0.4 + idx * 0.1 }}
                      className="flex items-start gap-3 text-sm text-white/55"
                    >
                      <motion.span
                        whileHover={{ scale: 1.2 }}
                        className="flex-shrink-0 w-6 h-6 rounded-lg bg-gradient-to-br from-secondary to-orange-300 text-white text-xs flex items-center justify-center font-bold mt-0.5 shadow-sm"
                      >
                        {idx + 1}
                      </motion.span>
                      <span className="leading-relaxed">{item}</span>
                    </motion.li>
                  ))}
                </ul>
              </div>
            </motion.div>

            {/* Tagline card */}
            <motion.div
              initial={{ opacity: 0, y: 20 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ delay: 0.5 }}
              whileHover={{ y: -3 }}
              className="gradient-border-ob rounded-2xl bg-white/[0.02] p-6 text-center"
            >
              <p className="text-sm text-white/60 italic leading-relaxed">
                "Bersama membangun, bersama berkarya untuk mahasiswa FTI UAJY
                yang lebih baik."
              </p>
              <div className="mt-3 flex items-center justify-center gap-2">
                <Flame size={14} className="text-secondary" />
                <span className="text-xs uppercase tracking-[0.3em] bg-gradient-to-r from-secondary to-accent2 bg-clip-text text-transparent font-semibold">
                  SEMA FTI UAJY
                </span>
                <Waves size={14} className="text-accent2" />
              </div>
            </motion.div>
          </motion.div>
        </div>
      </div>
    </section>
  );
}
