import { useMemo } from "react";

function useLastMondays() {
  return useMemo(() => {
    const today = new Date();
    const day = today.getDay();

    const diff = day === 0 ? -6 : 1 - day;

    const monday = new Date(today);
    monday.setDate(today.getDate() + diff);

    if (day >= 1 && day <= 5) {
      monday.setDate(monday.getDate() - 7);
    }

    const formatDate = (date) =>
      date.toLocaleDateString("fr-FR", {
        day: "2-digit",
        month: "2-digit",
        year: "numeric",
      });

    const mondays = [];

    for (let i = 0; i < 4; i++) {
      const d = new Date(monday);
      d.setDate(monday.getDate() - i * 7);
      mondays.push(formatDate(d)); // déjà du plus récent → ancien
    }

    return mondays;
  }, []);
}

export default useLastMondays;