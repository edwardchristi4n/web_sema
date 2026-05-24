import { apiRequest } from "./apiClient.js";

export async function login(payload) {
  return apiRequest({ url: "/auth.php", method: "post", data: payload });
}

export async function getSession() {
  const result = await apiRequest({ url: "/auth.php", method: "get" });
  return result.success === true ? result.data : null;
}

export async function logout() {
  return apiRequest({ url: "/auth.php", method: "delete" });
}
