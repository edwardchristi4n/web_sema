import { Routes, Route } from "react-router-dom";
import MainLayout from "./layouts/MainLayout.jsx";
import AdminLayout from "./layouts/AdminLayout.jsx";
import HomePage from "./pages/HomePage.jsx";
import DivisionDetailPage from "./pages/DivisionDetailPage.jsx";
import GaleryPage from "./pages/GaleryPage.jsx";
import LoginPage from "./pages/admin/LoginPage.jsx";
import DashboardPage from "./pages/admin/DashboardPage.jsx";
import DivisiAdminPage from "./pages/admin/DivisiAdminPage.jsx";
import EventAdminPage from "./pages/admin/EventAdminPage.jsx";
import MemberAdminPage from "./pages/admin/MemberAdminPage.jsx";
import ProkerAdminPage from "./pages/admin/ProkerAdminPage.jsx";
import RequireAuth from "./components/auth/RequireAuth.jsx";

export default function App() {
  return (
    <Routes>
      <Route element={<MainLayout />}>
        <Route path="/" element={<HomePage />} />
        <Route path="/bidang/:id" element={<DivisionDetailPage />} />
        <Route path="/galeri" element={<GaleryPage />} />
      </Route>

      <Route path="/admin/login" element={<LoginPage />} />

      <Route
        path="/admin"
        element={
          <RequireAuth>
            <AdminLayout />
          </RequireAuth>
        }
      >
        <Route index element={<DashboardPage />} />
        <Route path="divisi" element={<DivisiAdminPage />} />
        <Route path="event" element={<EventAdminPage />} />
        <Route path="member" element={<MemberAdminPage />} />
        <Route path="proker" element={<ProkerAdminPage />} />
      </Route>

      <Route path="*" element={<MainLayout />}>
        <Route path="*" element={<HomePage />} />
      </Route>
    </Routes>
  );
}
