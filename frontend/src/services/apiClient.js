import axios from "axios";

const baseURL =
  import.meta.env.VITE_API_BASE_URL || "http://localhost/Web_SEMA/backend/api";

export const apiClient = axios.create({
  baseURL,
  withCredentials: true,
  headers: {
    "Content-Type": "application/json",
  },
});

export async function apiRequest(config) {
  try {
    const response = await apiClient.request(config);
    return response.data;
  } catch (error) {
    const message = error?.response?.data?.message || "Request failed.";
    return { success: false, message };
  }
}
