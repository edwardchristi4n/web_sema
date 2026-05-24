import { useState } from "react";
import { useLocation, useNavigate } from "react-router-dom";
import Button from "../../components/ui/Button.jsx";
import Input from "../../components/ui/Input.jsx";
import { login } from "../../services/authService.js";
import { useToast } from "../../components/ui/Toast.jsx";

export default function LoginPage() {
  const navigate = useNavigate();
  const location = useLocation();
  const { addToast } = useToast();
  const [form, setForm] = useState({ username: "", password: "" });
  const [loading, setLoading] = useState(false);

  const handleSubmit = async (event) => {
    event.preventDefault();
    setLoading(true);
    const result = await login(form);
    setLoading(false);

    if (result.success) {
      addToast("Login berhasil.", "success");
      const target = location.state?.from || "/admin";
      navigate(target, { replace: true });
    } else {
      addToast(result.message || "Login gagal.", "error");
    }
  };

  return (
    <div className="flex min-h-screen items-center justify-center bg-night px-6">
      <div className="absolute inset-0 opacity-30 pointer-events-none bg-mesh-dark" />
      <div className="relative w-full max-w-md rounded-2xl border border-white/10 bg-midnight/95 backdrop-blur-sm p-8 shadow-glow animate-fade-in">
        <div className="text-center">
          <p className="text-xs uppercase tracking-[0.4em] text-white/50 font-semibold">
            SEMA ADMIN
          </p>
          <h1 className="mt-3 text-3xl font-bold">Masuk Dashboard</h1>
          <p className="mt-2 text-sm text-white/60">
            Gunakan akun admin resmi.
          </p>
        </div>
        <form onSubmit={handleSubmit} className="mt-8 space-y-4">
          <Input
            placeholder="Username"
            value={form.username}
            onChange={(event) =>
              setForm((prev) => ({ ...prev, username: event.target.value }))
            }
            required
          />
          <Input
            type="password"
            placeholder="Password"
            value={form.password}
            onChange={(event) =>
              setForm((prev) => ({ ...prev, password: event.target.value }))
            }
            required
          />
          <Button type="submit" className="w-full" disabled={loading}>
            {loading ? "Memproses..." : "Masuk"}
          </Button>
        </form>
      </div>
    </div>
  );
}
