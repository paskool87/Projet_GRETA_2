function useAlphabeticSort(data, key) {
  return [...data].sort((a, b) =>
    a[key].localeCompare(b[key], "fr", {
      sensitivity: "base",
    }),
  );
}

export default useAlphabeticSort;