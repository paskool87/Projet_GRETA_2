import { Link } from "react-router-dom";
import { useParams } from "react-router-dom";
import useSaveLocation from "../../hooks/useSaveLocation";
import useGetCachedAlternants from "../../hooks/useGetCachedAlternants";


import PrincipalTitle from "../../components/PrincipalTitle/PrincipalTitle";
import GetLastWeek from "../../components/GetLastWeek/GetLastWeek";
import Navbar from "../../components/Navbar/Navbar";
import Button from "../../components/Button/Button";
import RetourButton from "../../components/RetourButton/RetourButton";

import "./FicheAlternant.scss";

function FicheAlternant() {
  const { alternantId } = useParams();
  const saveLocation = useSaveLocation("ficheHebdo");

  const cachedAlternants = useGetCachedAlternants();

    const listeAlternants = (cachedAlternants || []).map((alt) => {
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





  const alternant = listeAlternants.find(
    (alt) => alt.alternantId === Number(alternantId),
  );
  return (
    <>
      <Navbar />
      <RetourButton type="noms" text="Retour à la liste de noms" />

      <div>
        <PrincipalTitle />
        <GetLastWeek />
        <h1>Fiche Alternant</h1>
        <p>
          {alternant?.nom} {alternant?.prenom} - {alternant?.formation}
        </p>
        <button className="fiche-alternant-button">
          <Link to={`/PastWeeks/${alternantId}`} onClick={saveLocation}>
            Voir les semaines précédentes
          </Link>
        </button>
      </div>
    </>
  );
}

export default FicheAlternant;
