import { Link } from "react-router-dom";
import { useParams } from "react-router-dom";

import PrincipalTitle from "../../components/PrincipalTitle/PrincipalTitle";
import GetLastWeek from "../../components/GetLastWeek/GetLastWeek";
import Navbar from "../../components/Navbar/Navbar";
import Button from "../../components/Button/Button";
import RetourButton from "../../components/RetourButton/RetourButton";

import "./Fiches.scss";

function Fiches() {
  const { alternantId, ficheId } = useParams();

  const data = localStorage.getItem("cache_api");

  const datas = data ? JSON.parse(data) : [];

  console.log("Données récupérées dans Fiches :", datas);

  const fichesAlternant = Array.isArray(datas) ? datas : [datas];

  console.log("Données après vérification de tableau dans Fiches :", fichesAlternant);

  const fiches = fichesAlternant?.map((alt) => alt.fiche || alt.fiches || [])?.flat() || [];

  console.log("Fiches des alternants dans Fiches :", fiches);

  const alternant = fichesAlternant.find((alt) => Number(alt.alternant.id) === Number(alternantId));

  console.log("Alternant trouvé dans Fiches :", alternant);


  const fiche = fiches.find((fic) => Number(fic.id) === Number(ficheId));

  console.log("Fiche trouvée :", fiche);


  return (
    <>
      <Navbar />
      <RetourButton type="noms" text="Retour à la liste de noms" />
      <RetourButton type="ficheHebdo" text="Retour à la  fiche" />
      <RetourButton type="fiches" text="Retour à la liste de fiches" />

      <div>
        <h1>Fiche de</h1>
        <p>
          {alternant?.alternant.utilisateur.nom}{" "}
          {alternant?.alternant.utilisateur.prenom}
        </p>
        <p>
          semaine du:{" "}
           {fiche?.date_debut &&
            new Date(fiche.date_debut).toLocaleDateString("fr-FR")}
        </p>
      </div>
    </>
  );
}

export default Fiches;
