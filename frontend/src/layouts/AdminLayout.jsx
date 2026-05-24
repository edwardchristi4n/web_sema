import { NavLink, Outlet, useNavigate } from "react-router-dom";
import { logout } from "../services/authService.js";

const navItems = [
  { label: "Dashboard", to: "/admin" },
  { label: "Divisi", to: "/admin/divisi" },
  { label: "Event", to: "/admin/event" },
  { label: "Member", to: "/admin/member" },
  { label: "Proker", to: "/admin/proker" },
];

export default function AdminLayout() {
  const navigate = useNavigate();

  const handleLogout = async () => {
    await logout();
    navigate("/admin/login");
  };

  return (
    <div className="min-h-screen bg-night text-white">
      <div className="flex min-h-screen">
        <aside className="hidden w-64 border-r border-white/10 bg-midnight/80 backdrop-blur-sm p-6 md:block">
          <div className="flex items-center gap-3 mb-8">
            <div className="h-10 w-10 rounded-xl bg-gradient-accent text-night flex items-center justify-center font-semibold text-sm">
              SE
            </div>
            <div>
              <p className="text-xs uppercase tracking-[0.35em] text-white/50">
                SEMA
              </p>
              <p className="text-sm font-semibold">Admin Panel</p>
            </div>
          </div>
          <nav className="space-y-2 text-sm">
            {navItems.map((item) => (
              <NavLink
                key={item.to}
                to={item.to}
                end={item.to === "/admin"}
                className={({ isActive }) =>
                  `flex items-center justify-between rounded-xl px-4 py-3 transition-all duration-200 ${
                    isActive
                      ? "bg-accent/20 text-white border border-accent/40"
                      : "text-white/60 hover:bg-white/5 hover:text-white"
                  }`
                }
              >
                {item.label}
                {item.to !== "/admin" && (
                  <span className="h-1.5 w-1.5 rounded-full bg-accent/60" />
                )}
              </NavLink>
            ))}
          </nav>
          <button
            type="button"
            onClick={handleLogout}
            className="mt-8 w-full rounded-lg bg-accent/80 px-4 py-2.5 text-sm font-semibold text-night transition-all hover:bg-accent active:bg-accent/80"
          >
            Keluar
          </button>
        </aside>

        <div className="flex-1">
          <header className="sticky top-0 z-30 border-b border-white/10 bg-night/80 backdrop-blur">
            <div className="flex items-center justify-between px-6 py-4">
              <div>
                <p className="text-xs uppercase tracking-[0.35em] text-white/50">
                  SEMA ADMIN
                </p>
                <h1 className="text-2xl font-semibold">Dashboard</h1>
              </div>
              <button
                type="button"
                onClick={handleLogout}
                className="rounded-full border border-white/20 px-4 py-2 text-sm text-white/70 hover:text-white"
              >
                Logout
              </button>
            </div>
          </header>
          <main className="px-6 py-8">
            <Outlet />
          </main>
        </div>
      </div>
    </div>
  );
}
