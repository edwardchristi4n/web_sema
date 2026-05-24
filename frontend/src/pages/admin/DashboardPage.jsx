import { useEffect, useState } from "react";
import Card from "../../components/ui/Card.jsx";
import Skeleton from "../../components/ui/Skeleton.jsx";
import { fetchDivisiList } from "../../services/divisiService.js";
import { fetchEventList } from "../../services/eventService.js";
import { fetchMemberList } from "../../services/memberService.js";

export default function DashboardPage() {
  const [loading, setLoading] = useState(true);
  const [counts, setCounts] = useState({ divisi: 0, event: 0, member: 0 });
  const [events, setEvents] = useState([]);

  useEffect(() => {
    const run = async () => {
      const [divRes, eventRes, memberRes] = await Promise.all([
        fetchDivisiList(),
        fetchEventList(),
        fetchMemberList(),
      ]);

      setCounts({
        divisi: divRes.success ? divRes.data.length : 0,
        event: eventRes.success ? eventRes.data.length : 0,
        member: memberRes.success ? memberRes.data.length : 0,
      });
      setEvents(eventRes.success ? eventRes.data.slice(0, 4) : []);
      setLoading(false);
    };

    run();
  }, []);

  if (loading) {
    return (
      <div className="grid gap-4 md:grid-cols-3">
        {[...Array(3)].map((_, idx) => (
          <Skeleton key={idx} className="h-28" />
        ))}
      </div>
    );
  }

  return (
    <div className="space-y-8">
      <div className="grid gap-4 md:grid-cols-3">
        <Card>
          <p className="text-xs uppercase tracking-[0.3em] text-white/50">
            Divisi
          </p>
          <p className="mt-3 text-3xl font-semibold text-accent">
            {counts.divisi}
          </p>
          <p className="mt-2 text-xs text-white/50">Total divisi aktif</p>
        </Card>
        <Card>
          <p className="text-xs uppercase tracking-[0.3em] text-white/50">
            Event
          </p>
          <p className="mt-3 text-3xl font-semibold text-amber-400">
            {counts.event}
          </p>
          <p className="mt-2 text-xs text-white/50">Agenda terpublikasi</p>
        </Card>
        <Card>
          <p className="text-xs uppercase tracking-[0.3em] text-white/50">
            Member
          </p>
          <p className="mt-3 text-3xl font-semibold text-emerald-400">
            {counts.member}
          </p>
          <p className="mt-2 text-xs text-white/50">Anggota terdaftar</p>
        </Card>
      </div>

      <Card>
        <div className="flex items-center justify-between">
          <h3 className="text-lg font-semibold">Event Terbaru</h3>
          <span className="text-xs text-white/40">{events.length} event</span>
        </div>
        <div className="mt-4 space-y-3">
          {events.length === 0 ? (
            <p className="text-sm text-white/50">Belum ada event.</p>
          ) : (
            events.map((event) => (
              <div
                key={event.id_event}
                className="flex items-center justify-between rounded-xl border border-white/10 bg-white/5 px-4 py-3"
              >
                <div>
                  <p className="text-sm font-semibold">{event.judul}</p>
                  <p className="text-xs text-white/50">
                    {event.tanggal || "-"}
                  </p>
                </div>
              </div>
            ))
          )}
        </div>
      </Card>
    </div>
  );
}
