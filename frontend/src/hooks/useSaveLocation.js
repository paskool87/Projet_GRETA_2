import { useLocation } from "react-router-dom";

function useSaveLocation(type) {
  const location = useLocation();

  function save() {
    const existing =
      JSON.parse(localStorage.getItem("returnTo")) || {};

    localStorage.setItem(
      "returnTo",
      JSON.stringify({
        ...existing,
        [type]: location.pathname,
      })
    );
  }

  return save;
}

export default useSaveLocation;