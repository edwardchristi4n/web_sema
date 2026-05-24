import { apiClient } from "./apiClient.js";

export async function uploadFile(file, type) {
  const formData = new FormData();
  formData.append("file", file);
  formData.append("type", type);

  try {
    const response = await apiClient.post("/upload.php", formData, {
      headers: { "Content-Type": "multipart/form-data" },
    });
    return response.data;
  } catch (error) {
    const message = error?.response?.data?.message || "Upload failed.";
    return { success: false, message };
  }
}
