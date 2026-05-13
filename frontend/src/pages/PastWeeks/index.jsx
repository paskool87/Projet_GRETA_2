import { useParams } from "react-router-dom";
import { useState } from "react";
import { Link } from "react-router-dom";
import useSaveLocation from "../../hooks/useSaveLocation";
import useFetch from "../../hooks/useFetch";
import { BASE_URL } from "../../config";

import Title from "../../components/Title/Title";
import GetLastWeek from "../../components/GetLastWeek/GetLastWeek";
import Navbar from "../../components/Navbar/Navbar";
import RetourButton from "../../components/RetourButton/RetourButton";
import StatusIcons from "../../components/StatusIcons/StatusIcons";

import "./PastWeeks.scss";

function PastWeeks() {
  const { alternantId } = useParams();
  const saveLocation = useSaveLocation("fiches");

  const [statut, setStatut] = useState("");
  const [mois, setMois] = useState("");
  const [annee, setAnnee] = useState("");

  const { data, loading, error } = useFetch(
    `${BASE_URL}/api/fiche`,
    "cache_api",
  );

  if (loading) return <p>Chargement...</p>;
  if (error) return <p>Erreur : {error.message}</p>;

  console.log("Données récupérées :", data);

  const datas = Array.isArray(data) ? data : [data];

  console.log("Données après vérification de tableau :", datas);

  const alternant = datas.find(
    (alt) => Number(alt.alternant.id) === Number(alternantId),
  );

  const alternantArray = Array.isArray(alternant) ? alternant : [alternant];

  console.log("Alternant trouvé :", alternantArray);

  const fiches = alternantArray
    ? alternantArray[0].fiches || alternantArray[0].fiche || []
    : [];
  console.log("Fiches de l'alternant :", fiches);

  const fichesFiltrees = fiches
    .filter((fiche) => {
      const date = new Date(fiche.date_debut);

      const ficheMois = String(date.getMonth() + 1).padStart(2, "0");
      const ficheAnnee = String(date.getFullYear());

      const matchMois = mois ? ficheMois === mois : true;
      const matchAnnee = annee ? ficheAnnee === annee : true;
      const matchStatut = statut ? fiche.status_fiche === statut : true;

      return matchMois && matchAnnee && matchStatut;
    })
    .reverse();

  return (
    <>
      <Navbar />
      <RetourButton type="noms" text="Retour à la liste de noms" />
      <RetourButton type="ficheHebdo" text="Retour à la  fiche" />

      <div className="past-weeks">
        <Title title="Semaines précédentes" />
        {alternant && (
          <h3>
            {alternantArray[0]?.alternant.utilisateur.prenom}{" "}
            {alternantArray[0]?.alternant.utilisateur.nom}
          </h3>
        )}

        <div className="filters">
          <select
            value={mois}
            onChange={(e) => setMois(e.target.value)}
            className="filters-month"
          >
            <option value="">Tous les mois</option>
            <option value="01">Janvier</option>
            <option value="02">Février</option>
            <option value="03">Mars</option>
            <option value="04">Avril</option>
            <option value="05">Mai</option>
            <option value="06">Juin</option>
            <option value="07">Juillet</option>
            <option value="08">Août</option>
            <option value="09">Septembre</option>
            <option value="10">Octobre</option>
            <option value="11">Novembre</option>
            <option value="12">Décembre</option>
          </select>

          <select
            value={annee}
            onChange={(e) => setAnnee(e.target.value)}
            className="filters-year"
          >
            <option value="">années</option>
            <option value="2026">2026</option>
            <option value="2027">2027</option>
            <option value="2028">2028</option>
            <option value="2029">2029</option>
            <option value="2030">2030</option>
            <option value="2031">2031</option>
            <option value="2032">2032</option>
            <option value="2033">2033</option>
            <option value="2034">2034</option>
            <option value="2035">2035</option>
            <option value="2036">2036</option>
            <option value="2037">2037</option>
            <option value="2038">2038</option>
            <option value="2039">2039</option>
            <option value="2040">2040</option>
          </select>

          <select value={statut} onChange={(e) => setStatut(e.target.value)}>
            <option value="">Tous les statuts</option>
            <option value="VALIDE">Validée</option>
            <option value="SOUMISE">En attente</option>
            <option value="BROUILLON">Brouillon</option>
            <option value="CRITERES_NON_REMPLIS">Critères non remplis</option>
          </select>
        </div>

        <div className="past-weeks-list">
          {fiches.length === 0 ? (
            <p>Aucune fiche trouvée</p>
          ) : (
            fichesFiltrees.map((fiche) => (
              <Link
                key={fiche.id}
                to={`/Fiches/${alternantId}/${fiche.id}`}
                onClick={saveLocation}
                className="past-weeks-list-link"
              >
                <div className="past-weeks-list-item">
                  <p>
                    Semaine du :{" "}
                    {fiche?.date_debut &&
                      new Date(fiche.date_debut).toLocaleDateString("fr-FR")}
                  </p>
                  <p className=" past-weeks-list-item-status">
                    Statut : {fiche.status_fiche}
                    <StatusIcons status={fiche.status_fiche} />
                  </p>
                </div>
              </Link>
            ))
          )}
        </div>
      </div>
    </>
  );
}

export default PastWeeks;
