import { useEffect, useState, useCallback } from "react";
import { motion, AnimatePresence } from "framer-motion";
import { ArrowRight, Sparkles, ChevronDown, Zap, Users, Award } from "lucide-react";
import Button from "../ui/Button.jsx";
import { assetUrl } from "../../utils/asset.js";

const images = [
  assetUrl("/asset/img/seminar%20backdrop.JPG"),
  assetUrl("/asset/img/spfest.JPG"),
  assetUrl("/asset/img/baksos.JPG"),
];

const containerVariants = {
  hidden: { opacity: 0 },
  visible: {
    opacity: 1,
    transition: {
      staggerChildren: 0.15,
      delayChildren: 0.4,
    },
  },
};

const itemVariants = {
  hidden: { opacity: 0, y: 40, filter: "blur(10px)" },
  visible: {
    opacity: 1,
    y: 0,
    filter: "blur(0px)",
    transition: { duration: 0.9, ease: [0.25, 0.1, 0.25, 1] },
  },
};

function FloatingOrb({ className, delay = 0, colors = "bg-secondary/20" }) {
  return (
    <motion.div
      initial={{ opacity: 0, scale: 0.5 }}
      animate={{
        opacity: [0.1, 0.25, 0.1],
        scale: [1, 1.3, 1],
        y: [0, -40, 0],
        x: [0, 15, 0],
      }}
      transition={{
        duration: 10,
        delay,
        repeat: Infinity,
        ease: "easeInOut",
      }}
      className={`absolute rounded-full blur-3xl pointer-events-none ${colors} ${className}`}
    />
  );
}

function ParticleField() {
  const particles = Array.from({ length: 20 }, (_, i) => ({
    id: i,
    x: Math.random() * 100,
    y: Math.random() * 100,
    size: Math.random() * 3 + 1,
    delay: Math.random() * 5,
    duration: Math.random() * 6 + 6,
    isOrange: Math.random() > 0.5,
  }));

  return (
    <div className="absolute inset-0 overflow-hidden pointer-events-none">
      {particles.map((p) => (
        <motion.div
          key={p.id}
          initial={{ opacity: 0, y: 0 }}
          animate={{
            opacity: [0, 0.6, 0],
            y: [0, -80, -160],
            x: [0, Math.random() * 30 - 15],
          }}
          transition={{
            duration: p.duration,
            delay: p.delay,
            repeat: Infinity,
            ease: "easeOut",
          }}
          style={{
            left: `${p.x}%`,
            top: `${p.y}%`,
            width: p.size,
            height: p.size,
          }}
          className={`absolute rounded-full ${
            p.isOrange ? "bg-secondary" : "bg-accent2"
          }`}
        />
      ))}
    </div>
  );
}

export default function HeroSection() {
  const [active, setActive] = useState(0);
  const [direction, setDirection] = useState(1);

  const nextSlide = useCallback(() => {
    setDirection(1);
    setActive((prev) => (prev + 1) % images.length);
  }, []);

  useEffect(() => {
    const id = setInterval(nextSlide, 5000);
    return () => clearInterval(id);
  }, [nextSlide]);

  const goToSlide = (idx) => {
    setDirection(idx > active ? 1 : -1);
    setActive(idx);
  };

  return (
    <section
      id="home"
      className="relative min-h-screen overflow-hidden bg-night"
    >
      {/* Background images with crossfade */}
      <div className="absolute inset-0">
        <AnimatePresence mode="wait">
          <motion.div
            key={active}
            initial={{ opacity: 0, scale: 1.1 }}
            animate={{ opacity: 1, scale: 1 }}
            exit={{ opacity: 0 }}
            transition={{ duration: 1.5, ease: "easeInOut" }}
            className="absolute inset-0 bg-cover bg-center"
            style={{ backgroundImage: `url(${images[active]})` }}
          />
        </AnimatePresence>
        {/* Multi-layer gradient overlay */}
        <div className="absolute inset-0 bg-gradient-to-b from-night/90 via-night/60 to-night" />
        <div className="absolute inset-0 bg-gradient-to-r from-night/40 via-transparent to-night/40" />
        {/* Orange-blue color wash */}
        <div className="absolute inset-0 bg-gradient-to-br from-secondary/10 via-transparent to-accent2/10 mix-blend-overlay" />
      </div>

      {/* Floating orbs */}
      <FloatingOrb className="w-[500px] h-[500px] -top-32 -right-32" delay={0} colors="bg-secondary/15" />
      <FloatingOrb className="w-[400px] h-[400px] bottom-0 -left-32" delay={2} colors="bg-accent2/15" />
      <FloatingOrb className="w-[300px] h-[300px] top-1/3 right-1/4" delay={4} colors="bg-secondary/10" />
      <FloatingOrb className="w-[200px] h-[200px] top-1/2 left-1/3" delay={3} colors="bg-accent2/10" />

      {/* Particle field */}
      <ParticleField />

      {/* Grid pattern overlay */}
      <div
        className="absolute inset-0 pointer-events-none opacity-[0.03]"
        style={{
          backgroundImage: `linear-gradient(rgba(255,255,255,0.1) 1px, transparent 1px),
                           linear-gradient(90deg, rgba(255,255,255,0.1) 1px, transparent 1px)`,
          backgroundSize: "60px 60px",
        }}
      />

      {/* Content */}
      <div className="relative z-10 mx-auto flex min-h-screen max-w-6xl flex-col justify-center px-6 py-24">
        <motion.div
          className="max-w-3xl"
          variants={containerVariants}
          initial="hidden"
          animate="visible"
        >
          {/* Badge */}
          <motion.div variants={itemVariants} className="mb-6">
            <motion.div
              className="inline-flex items-center gap-2 rounded-full border border-secondary/30 bg-secondary/10 px-4 py-2"
              whileHover={{ scale: 1.05, borderColor: "rgba(255,107,53,0.5)" }}
            >
              <motion.div
                animate={{ rotate: [0, 15, -15, 0] }}
                transition={{ duration: 2, repeat: Infinity, ease: "easeInOut" }}
              >
                <Sparkles size={14} className="text-secondary" />
              </motion.div>
              <p className="text-xs uppercase tracking-[0.4em] text-secondary font-semibold">
                Satu Hati, Satu Jiwa
              </p>
            </motion.div>
          </motion.div>

          {/* Title */}
          <motion.h1
            variants={itemVariants}
            className="text-4xl font-extrabold leading-[1.1] md:text-6xl lg:text-7xl"
          >
            Senat Mahasiswa{" "}
            <span className="gradient-text-orange-blue">FTI UAJY</span>
          </motion.h1>

          {/* Description */}
          <motion.p
            variants={itemVariants}
            className="mt-6 text-base text-white/60 md:text-lg leading-relaxed max-w-2xl"
          >
            Wadah aspirasi, kolaborasi, dan kreativitas mahasiswa Fakultas
            Teknologi Industri UAJY dengan pendekatan yang modern dan inklusif.
          </motion.p>

          {/* CTA Buttons */}
          <motion.div
            variants={itemVariants}
            className="mt-10 flex flex-wrap gap-4"
          >
            <a href="#tentang">
              <Button size="lg" variant="secondary">
                Tentang Kami
                <ArrowRight size={18} />
              </Button>
            </a>
            <a href="#kontak">
              <Button size="lg" variant="blue">
                Hubungi Kami
              </Button>
            </a>
          </motion.div>

          {/* Stats strip */}
          <motion.div
            variants={itemVariants}
            className="mt-14 flex flex-wrap gap-10"
          >
            {[
              { value: "5", label: "Bidang", icon: Zap, color: "from-secondary to-orange-300" },
              { value: "12+", label: "Komunitas", icon: Users, color: "from-accent2 to-blue-300" },
              { value: "30+", label: "Program", icon: Award, color: "from-secondary to-accent2" },
            ].map((stat, idx) => {
              const StatIcon = stat.icon;
              return (
                <motion.div
                  key={stat.label}
                  className="flex items-center gap-3"
                  whileHover={{ y: -3 }}
                  transition={{ duration: 0.2 }}
                >
                  <div className={`flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br ${stat.color} bg-opacity-20`}>
                    <StatIcon size={18} className="text-white/90" />
                  </div>
                  <div className="flex flex-col">
                    <span className={`text-2xl font-bold bg-gradient-to-r ${stat.color} bg-clip-text text-transparent`}>
                      {stat.value}
                    </span>
                    <span className="text-[10px] uppercase tracking-[0.3em] text-white/40">
                      {stat.label}
                    </span>
                  </div>
                </motion.div>
              );
            })}
          </motion.div>
        </motion.div>
      </div>

      {/* Slide indicators */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ delay: 1.2 }}
        className="absolute bottom-12 left-1/2 z-10 flex -translate-x-1/2 items-center gap-3"
      >
        {images.map((_, idx) => (
          <motion.button
            key={idx}
            type="button"
            onClick={() => goToSlide(idx)}
            whileHover={{ scale: 1.2 }}
            whileTap={{ scale: 0.95 }}
            className={`relative h-2 rounded-full transition-all duration-500 ${
              idx === active
                ? "w-12 bg-gradient-to-r from-secondary to-accent2"
                : "w-8 bg-white/20 hover:bg-white/40"
            }`}
            aria-label={`Slide ${idx + 1}`}
          >
            {idx === active && (
              <motion.div
                layoutId="activeIndicator"
                className="absolute inset-0 rounded-full bg-gradient-to-r from-secondary to-accent2"
                transition={{ type: "spring", stiffness: 300, damping: 30 }}
              />
            )}
          </motion.button>
        ))}
      </motion.div>

      {/* Scroll hint */}
      <motion.div
        initial={{ opacity: 0 }}
        animate={{ opacity: 1 }}
        transition={{ delay: 2 }}
        className="absolute bottom-4 left-1/2 z-10 -translate-x-1/2"
      >
        <motion.div
          animate={{ y: [0, 8, 0] }}
          transition={{ duration: 2, repeat: Infinity, ease: "easeInOut" }}
        >
          <ChevronDown size={20} className="text-white/20" />
        </motion.div>
      </motion.div>
    </section>
  );
}
