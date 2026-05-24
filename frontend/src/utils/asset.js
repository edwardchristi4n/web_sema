const assetBase =
  import.meta.env.VITE_ASSET_BASE_URL || "http://localhost/Web_SEMA";

export function assetUrl(path) {
  if (!path) return "";
  if (path.startsWith("http")) return path;
  if (path.startsWith("/")) return `${assetBase}${path}`;
  return `${assetBase}/${path}`;
}
