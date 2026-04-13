import { useMemo } from "react";

function GetLastWeek() {

  const { monday, friday } = useMemo(() => {

    const today = new Date();
    const day = today.getDay();

    // Calcul du lundi de cette semaine
    const diff = day === 0 ? -6 : 1 - day;

    const monday = new Date(today);
    monday.setDate(today.getDate() + diff);

    // 🔥 Ajustement selon le jour
    // Si on est entre lundi (1) et vendredi (5)
    // → on recule d'une semaine
    if (day >= 1 && day <= 5) {
      monday.setDate(monday.getDate() - 7);
    }

    // Vendredi = lundi + 4 jours
    const friday = new Date(monday);
    friday.setDate(monday.getDate() + 4);

    return { monday, friday };

  }, []);

  function formatDate(date) {
    return date.toLocaleDateString('fr-FR');
  }

  return (
    <p>
      Semaine du {formatDate(monday)} au {formatDate(friday)}
    </p>
  );
}

export default GetLastWeek;