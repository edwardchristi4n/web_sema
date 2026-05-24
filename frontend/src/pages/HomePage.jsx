import { useEffect, useState } from "react";
import { motion, AnimatePresence } from "framer-motion";
import HeroSection from "../components/sections/HeroSection.jsx";
import DivisionSection from "../components/sections/DivisionSection.jsx";
import CommunitySection from "../components/sections/CommunitySection.jsx";
import NewsSection from "../components/sections/NewsSection.jsx";
import AboutSection from "../components/sections/AboutSection.jsx";
import ContactSection from "../components/sections/ContactSection.jsx";
import Skeleton from "../components/ui/Skeleton.jsx";
import { fetchDivisiList } from "../services/divisiService.js";
import { fetchEventList } from "../services/eventService.js";

function LoadingSkeleton() {
  return (
    <section className="bg-night py-20">
      <div className="mx-auto max-w-6xl px-6">
        <div className="text-center mb-10">
          <Skeleton className="h-4 w-32 mx-auto" />
          <Skeleton className="h-10 w-72 mx-auto mt-4" />
          <Skeleton className="h-4 w-96 mx-auto mt-4" />
        </div>
        <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
          {[...Array(3)].map((_, idx) => (
            <Skeleton key={idx} className="h-52 rounded-2xl" />
          ))}
        </div>
      </div>
    </section>
  );
}

export default function HomePage() {
  const [divisions, setDivisions] = useState([]);
  const [events, setEvents] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const run = async () => {
      const [divisiRes, eventRes] = await Promise.all([
        fetchDivisiList(),
        fetchEventList(3),
      ]);
      if (divisiRes.success) {
        setDivisions(divisiRes.data || []);
      }
      if (eventRes.success) {
        setEvents(eventRes.data || []);
      }
      setLoading(false);
    };

    run();
  }, []);

  return (
    <motion.div
      initial={{ opacity: 0 }}
      animate={{ opacity: 1 }}
      transition={{ duration: 0.5 }}
    >
      <HeroSection />
      <AnimatePresence mode="wait">
        {loading ? (
          <motion.div
            key="skeleton"
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0, y: -10 }}
          >
            <LoadingSkeleton />
          </motion.div>
        ) : (
          <motion.div
            key="content"
            initial={{ opacity: 0, y: 10 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.5 }}
          >
            <DivisionSection divisions={divisions} />
          </motion.div>
        )}
      </AnimatePresence>
      <CommunitySection />
      <NewsSection events={events} />
      <AboutSection />
      <ContactSection />
    </motion.div>
  );
}
