import { useEffect, useState } from "react";
import { useLocation, useNavigate } from "react-router-dom";
import { getSession } from "../../services/authService.js";

export default function RequireAuth({ children }) {
  const navigate = useNavigate();
  const location = useLocation();
  const [checking, setChecking] = useState(true);

  useEffect(() => {
    let active = true;
    const run = async () => {
      const ok = await getSession();
      if (!active) {
        return;
      }
      if (!ok) {
        navigate("/admin/login", {
          replace: true,
          state: { from: location.pathname },
        });
      } else {
        setChecking(false);
      }
    };

    run();
    return () => {
      active = false;
    };
  }, [location.pathname, navigate]);

  if (checking) {
    return (
      <div className="flex min-h-screen items-center justify-center text-white/60">
        Memverifikasi sesi...
      </div>
    );
  }

  return children;
}
