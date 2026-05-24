import { useEffect, useState } from "react";
import { motion } from "framer-motion";
import { ChevronDown } from "lucide-react";
import { fetchProkerList } from "../services/prokerService.js";
import { assetUrl } from "../utils/asset.js";
import Skeleton from "../components/ui/Skeleton.jsx";

export default function GaleryPage() {
  const [prokers, setProkers] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const run = async () => {
      const res = await fetchProkerList();
      if (res.success) {
        setProkers(res.data || []);
      }
      setLoading(false);
    };
    run();
  }, []);

  const containerVariants = {
    hidden: {},
    visible: { transition: { staggerChildren: 0.1 } },
  };

  const itemVariants = {
    hidden: { opacity: 0, y: 20 },
    visible: { opacity: 1, y: 0, transition: { duration: 0.6 } },
  };

  return (
    <div className="min-h-screen bg-night text-white">
      {/* Hero Section */}
      <section className="relative overflow-hidden pt-32 pb-20">
        {/* Decorative gradient */}
        <div className="absolute inset-0 pointer-events-none">
          <div className="absolute top-0 left-1/4 w-96 h-96 bg-secondary/10 rounded-full blur-[120px]" />
          <div className="absolute top-1/3 right-1/4 w-96 h-96 bg-accent2/10 rounded-full blur-[120px]" />
        </div>

        <div className="mx-auto max-w-6xl px-6 relative z-10">
          <motion.div
            initial={{ opacity: 0, y: 30 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.7 }}
            className="text-center mb-16"
          >
            <motion.p
              initial={{ opacity: 0, y: 10 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ delay: 0.1 }}
              className="inline-flex items-center gap-2 text-xs uppercase tracking-[0.4em] text-accent2 font-semibold mb-4"
            >
              <span className="h-1 w-4 bg-gradient-to-r from-secondary to-accent2 rounded-full" />
              Galeri Program Kerja
              <span className="h-1 w-4 bg-gradient-to-r from-accent2 to-secondary rounded-full" />
            </motion.p>
            <motion.h1
              initial={{ opacity: 0, y: 10 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ delay: 0.2 }}
              className="text-4xl md:text-5xl font-bold mb-4"
            >
              Jelajahi Program Kerja SEMA
            </motion.h1>
            <motion.p
              initial={{ opacity: 0, y: 10 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ delay: 0.3 }}
              className="text-lg text-white/70 max-w-2xl mx-auto mb-8"
            >
              Lihat berbagai program kerja dan inisiatif dari berbagai divisi
              SEMA FTI UAJY
            </motion.p>

            <motion.div
              initial={{ opacity: 0, y: 10 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ delay: 0.4 }}
              className="flex justify-center"
            >
              <motion.div
                animate={{ y: [0, 8, 0] }}
                transition={{ duration: 2, repeat: Infinity }}
              >
                <ChevronDown size={32} className="text-secondary/60" />
              </motion.div>
            </motion.div>
          </motion.div>
        </div>
      </section>

      {/* Program Kerja Grid */}
      <section className="relative py-20 bg-midnight">
        <div className="absolute top-0 left-0 right-0 section-divider" />
        <div className="mx-auto max-w-6xl px-6 relative z-10">
          {loading ? (
            <div className="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
              {[...Array(6)].map((_, idx) => (
                <Skeleton key={idx} className="h-80 rounded-2xl" />
              ))}
            </div>
          ) : prokers.length === 0 ? (
            <motion.div
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              className="text-center py-20"
            >
              <p className="text-white/50 text-lg">
                Belum ada program kerja yang ditampilkan
              </p>
            </motion.div>
          ) : (
            <motion.div
              variants={containerVariants}
              initial="hidden"
              animate="visible"
              className="grid gap-8 md:grid-cols-2 lg:grid-cols-3"
            >
              {prokers.map((proker) => (
                <motion.div
                  key={proker.id_proker}
                  variants={itemVariants}
                  className="group overflow-hidden rounded-2xl border border-white/10 bg-white/5 backdrop-blur-sm transition-all hover:border-secondary/40 hover:bg-white/10 hover:shadow-xl hover:shadow-secondary/10"
                >
                  {/* Image */}
                  {proker.gambar && (
                    <div className="relative h-48 overflow-hidden bg-gradient-to-br from-secondary/20 to-accent2/20">
                      <img
                        src={`${assetUrl("")}asset/uploads/proker/${proker.gambar}`}
                        alt={proker.nama_proker}
                        className="h-full w-full object-cover transition-transform group-hover:scale-105"
                      />
                    </div>
                  )}

                  {/* Content */}
                  <div className="p-6">
                    <h3 className="text-lg font-bold mb-2 line-clamp-2">
                      {proker.nama_proker}
                    </h3>
                    <p className="text-sm text-white/60 mb-4 line-clamp-3">
                      {proker.deskripsi || "Tidak ada deskripsi"}
                    </p>

                    {proker.nama_divisi && (
                      <div className="inline-flex items-center gap-2 rounded-full bg-secondary/10 px-3 py-1 text-xs font-semibold text-secondary">
                        <span className="h-1.5 w-1.5 rounded-full bg-secondary" />
                        {proker.nama_divisi}
                      </div>
                    )}
                  </div>
                </motion.div>
              ))}
            </motion.div>
          )}
        </div>
      </section>
    </div>
  );
}
