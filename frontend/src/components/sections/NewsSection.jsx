import { motion } from "framer-motion";
import { Calendar, MapPin, ArrowRight, Newspaper, Clock } from "lucide-react";
import Card from "../ui/Card.jsx";
import { formatDate } from "../../utils/format.js";
import { assetUrl } from "../../utils/asset.js";

const containerVariants = {
  hidden: {},
  visible: { transition: { staggerChildren: 0.12 } },
};

const cardVariants = {
  hidden: { opacity: 0, y: 40, filter: "blur(6px)" },
  visible: {
    opacity: 1,
    y: 0,
    filter: "blur(0px)",
    transition: { duration: 0.7, ease: [0.25, 0.1, 0.25, 1] },
  },
};

export default function NewsSection({ events }) {
  return (
    <section id="berita" className="relative bg-night py-24 overflow-hidden">
      {/* Decorative */}
      <div className="absolute top-0 left-0 right-0 section-divider" />
      <div className="absolute -right-32 top-20 w-96 h-96 bg-secondary/5 rounded-full blur-[120px] pointer-events-none" />
      <div className="absolute -left-32 bottom-20 w-96 h-96 bg-accent2/5 rounded-full blur-[120px] pointer-events-none" />

      <div className="mx-auto max-w-6xl px-6 relative z-10">
        <motion.div
          initial={{ opacity: 0, y: 30 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true, margin: "-100px" }}
          transition={{ duration: 0.7 }}
          className="flex flex-col gap-5 md:flex-row md:items-end md:justify-between"
        >
          <div>
            <motion.p
              initial={{ opacity: 0, y: 10 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ delay: 0.1 }}
              className="inline-flex items-center gap-2 text-xs uppercase tracking-[0.4em] text-secondary font-semibold"
            >
              <span className="w-8 h-[1px] bg-secondary/50" />
              Berita
            </motion.p>
            <h2 className="mt-5 text-3xl font-bold md:text-5xl">
              Update <span className="gradient-text-fire">Terbaru</span>
            </h2>
            <p className="mt-3 text-white/50 max-w-lg">
              Ikuti perkembangan kegiatan dan acara terbaru dari SEMA FTI UAJY.
            </p>
          </div>
          <motion.div
            initial={{ opacity: 0, x: 20 }}
            whileInView={{ opacity: 1, x: 0 }}
            viewport={{ once: true }}
            transition={{ delay: 0.3 }}
            className="flex items-center gap-2 rounded-xl border border-white/10 bg-white/[0.03] px-4 py-2.5"
          >
            <Clock size={14} className="text-secondary" />
            <span className="text-xs text-white/50">Diperbarui secara berkala</span>
          </motion.div>
        </motion.div>

        <motion.div
          className="mt-12 grid gap-6 md:grid-cols-2 lg:grid-cols-3"
          variants={containerVariants}
          initial="hidden"
          whileInView="visible"
          viewport={{ once: true, margin: "-50px" }}
        >
          {events.length === 0 ? (
            <motion.div
              variants={cardVariants}
              className="col-span-full"
            >
              <div className="flex flex-col items-center justify-center rounded-2xl border border-white/10 bg-white/[0.02] py-16 px-6">
                <motion.div
                  animate={{ y: [0, -8, 0] }}
                  transition={{ duration: 3, repeat: Infinity, ease: "easeInOut" }}
                >
                  <Newspaper size={40} className="text-white/20" />
                </motion.div>
                <p className="mt-4 text-white/40">
                  Belum ada berita yang dipublikasikan.
                </p>
              </div>
            </motion.div>
          ) : (
            events.map((event, idx) => (
              <motion.div key={event.id_event} variants={cardVariants}>
                <motion.div
                  whileHover={{ y: -6, scale: 1.02 }}
                  transition={{ duration: 0.3 }}
                  className="group relative flex h-full flex-col overflow-hidden rounded-2xl border border-white/10 bg-white/[0.03] transition-all duration-500 hover:border-secondary/30 hover:shadow-[0_0_40px_rgba(255,107,53,0.08)]"
                >
                  {/* Image */}
                  <div className="relative h-48 overflow-hidden">
                    {event.foto_event ? (
                      <motion.img
                        src={assetUrl(event.foto_event)}
                        alt={event.judul}
                        className="h-full w-full object-cover"
                        whileHover={{ scale: 1.08 }}
                        transition={{ duration: 0.6 }}
                      />
                    ) : (
                      <div className="h-full w-full bg-gradient-to-br from-secondary/10 via-accent2/5 to-transparent flex items-center justify-center">
                        <Newspaper size={32} className="text-white/10" />
                      </div>
                    )}
                    {/* Image overlay gradient */}
                    <div className="absolute inset-0 bg-gradient-to-t from-night/80 via-transparent to-transparent" />

                    {/* Date badge */}
                    <div className="absolute top-4 left-4 flex items-center gap-1.5 rounded-lg bg-night/70 backdrop-blur-md border border-white/10 px-3 py-1.5">
                      <Calendar size={12} className="text-secondary" />
                      <span className="text-[10px] uppercase tracking-[0.2em] text-white/70">
                        {formatDate(event.tanggal)}
                      </span>
                    </div>
                  </div>

                  {/* Content */}
                  <div className="flex flex-1 flex-col p-6">
                    <h3 className="text-lg font-bold leading-tight group-hover:text-secondary transition-colors duration-300">
                      {event.judul}
                    </h3>
                    <p className="mt-3 text-sm text-white/45 flex-1 leading-relaxed">
                      {event.deskripsi
                        ? event.deskripsi.slice(0, 100) + (event.deskripsi.length > 100 ? "..." : "")
                        : "Event terbaru dari SEMA FTI UAJY."}
                    </p>
                    {event.lokasi && (
                      <p className="mt-3 flex items-center gap-1.5 text-xs text-white/35">
                        <MapPin size={12} className="text-accent2" />
                        {event.lokasi}
                      </p>
                    )}
                    <div className="mt-4 pt-4 border-t border-white/[0.06]">
                      <a
                        href="#"
                        className="inline-flex items-center gap-2 text-sm font-semibold text-secondary hover:text-white transition-colors group/link"
                      >
                        Baca selengkapnya
                        <motion.span
                          className="inline-block"
                          animate={{ x: [0, 3, 0] }}
                          transition={{ duration: 1.5, repeat: Infinity }}
                        >
                          <ArrowRight size={14} />
                        </motion.span>
                      </a>
                    </div>
                  </div>
                </motion.div>
              </motion.div>
            ))
          )}
        </motion.div>
      </div>
    </section>
  );
}
