import { Link } from "react-router-dom";
import useSaveLocation from "../../hooks/useSaveLocation";
import useFetch from "../../hooks/useFetch";
import { BASE_URL } from "../../config";

import PrincipalTitle from "../../components/PrincipalTitle/PrincipalTitle";
import GetLastWeek from "../../components/GetLastWeek/GetLastWeek";
import Navbar from "../../components/Navbar/Navbar";

import "./TuteurProf.scss";

function TuteurProf() {
  const saveLocation = useSaveLocation("noms");

  const { data, loading, error } = useFetch(
    `${BASE_URL}/api/fiche`,
    "cache_api",
  );

  if (loading) return <p>Chargement...</p>;
  if (error) return <p>Erreur : {error.message}</p>;

  const listeAlternants = data.map((alt) => {
    const derniere_fiche =
      alt.fiche && alt.fiche.length > 0
        ? alt.fiche[alt.fiche.length - 1]
        : null;

    return {
      alternantId: alt.alternant.id,
      nom: alt.alternant.utilisateur.nom,
      prenom: alt.alternant.utilisateur.prenom,
      formation: alt.formation?.nom_formation,
      status: derniere_fiche?.status_fiche ?? null,
    };
  });

  console.log("Liste des alternants :", listeAlternants);

  return (
    <>
      <Navbar />

      <div className="tuteur-prof">
        <PrincipalTitle />

        <h2>
          <GetLastWeek /> à valider
        </h2>
        <div className="tuteur-prof-caption">
          <span>Alternants</span>
          <span>Statut</span>
          <span>Groupe</span>
        </div>
        <div className="tuteur-prof-liste">
          {listeAlternants.length === 0 ? (
            <p>Aucun alternant</p>
          ) : (
            listeAlternants.map((alt) => (
              <div key={alt.nom} className="alternant-item ligne">
                <Link
                  to={`/FicheAlternant/${alt.alternantId}`}
                  onClick={saveLocation}
                  className="alternant-link"
                 >
                  <span className="alternant-link-name">{alt.nom} {alt.prenom}</span>
                </Link>

                <span>{alt.status}</span>
                <span>{alt.formation}</span>
              </div>
            ))
          )}
        </div>
      </div>
    </>
  );
}

export default TuteurProf;
