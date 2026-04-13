function useGetCachedAlternants() {
  const prefix = "cache";

  const key = Object.keys(localStorage).find((k) =>
    k.startsWith(prefix)
  );

  if (!key) return null;

  const cached = localStorage.getItem(key);
  return cached ? JSON.parse(cached) : null;
}

export default useGetCachedAlternants;