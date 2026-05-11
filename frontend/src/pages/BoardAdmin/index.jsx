import { useState } from "react";
import { Link } from "react-router-dom";
import useSaveLocation from "../../hooks/useSaveLocation";
import PrincipalTitle from "../../components/PrincipalTitle/PrincipalTitle";
import Navbar from "../../components/Navbar/Navbar";
import Button from "../../components/Button/Button";
import GetLastWeek from "../../components/GetLastWeek/GetLastWeek";
import StatusIcons from "../../components/StatusIcons/StatusIcons";


import useLastMondays from "../../hooks/useLastMondays";
import useFetch from "../../hooks/useFetch";
import { BASE_URL } from "../../config";


import { FakeApi_fiche } from "../../Datas/FakeApi_fiche"; // Importez les données factices
import alternants from "../../Datas/MakeAlternants"; // Importez les données factices

import "./BoardAdmin.scss";

function BoardAdmin() {
  const [filtre, setFiltre] = useState("Total alternants");
  const [groupe, setGroupe] = useState("Tous");
  const saveLocation = useSaveLocation("noms");
  const lastMondays = useLastMondays();

  // ne pas effacer l'appel API
  /*const { data, loading, error } = useFetch(
    `${BASE_URL}/api/fiche`,
    "cache_api",
  );

  if (loading) return <p>Chargement...</p>;
  if (error) return <p>Erreur : {error.message}</p>;*/

  const data = alternants; // Utilisez les données factices

  console.log("Données récupérées :", data);

  const listeAlternants = data.map((alt) => {
    const fiches = alt.fiche || [];

    // ajuster les variables pour correspondre à la semaine dernière(fiche à valider) (surement faire length -2 pour dernière_fiche)
    const derniere_fiche = fiches.length > 0 ? fiches[fiches.length - 1] : null;

    const allStatusFiches = fiches.map((f) => f.status_fiche);

    const pastFichesAlternants = fiches.slice(0, -1).map((f) => ({
      id: f.id,
      status: f.status_fiche,
      date: f.date_debut,
    }));

    const last4Fiches = [...fiches]
      .sort((a, b) => new Date(b.date_debut) - new Date(a.date_debut)) // plus récent d'abord
      .slice(0, 4)
      .map((f) => ({
        id: f.id,
        status: f.status_fiche,
        date: f.date_debut,
      }));

    return {
      alternantId: alt.alternant.id,
      nom: alt.alternant.utilisateur.nom,
      prenom: alt.alternant.utilisateur.prenom,
      formation: alt.formation?.nom_formation,
      formationId: alt.formation?.id,
      status: derniere_fiche?.status_fiche ?? null,
      pastFichesAlternant: pastFichesAlternants,
      last4FichesAlternant: last4Fiches,
      allStatusFichesAlternant: allStatusFiches,
    };
  });

  const mapping = {
    "Total alternants": () => true,

    "Fiche validée": (item) => item.status === "VALIDE",

    "En attente de validation": (item) => item.status === "SOUMISE",

    "Fiches non remplies": (item) => item.status === "BROUILLON",

    "Critères non remplis": (item) => item.status === "CRITERES_NON_REMPLIS",
  };
  const lignes = Object.keys(mapping);

  const groupes = [
    "Tous",
    ...new Set(listeAlternants.map((a) => a.formationId + " - " + a.formation)),
  ];

  const getCount = (titre) => {
    return listeAlternants.filter(mapping[titre]).length;
  };
  const getCountPast = (titre) => {
    return listeAlternants
      .flatMap((a) => a.pastFichesAlternant)
      .filter(mapping[titre]).length;
  };

  const dataFiltree = listeAlternants.filter((item) => {
    const matchStatut = mapping[filtre](item);
    const matchGroupe =
      groupe === "Tous" ||
      parseInt(item.formationId, 10) === parseInt(groupe, 10);
    return matchStatut && matchGroupe;
  });

  return (
    <>
      <Navbar />

      <Link to="/Inscriptions" className="inscriptions">
        <Button children="Inscriptions" />
      </Link>

      <div className="board-admin">
        <PrincipalTitle />

        <div className="board-admin-main">

          {/* vue globale semaine précédente */}
          <div className="board-admin-main-global">
            <GetLastWeek />

            {lignes.map((titre, index) => (
              <div key={index} className="board-admin-main-global-item">
                <h3>{titre}</h3>
                <p>{getCount(titre)}</p>
                <StatusIcons status={titre === "Fiche validée" ? "VALIDE" : titre === "En attente de validation" ? "SOUMISE" : titre === "Fiches non remplies" ? "BROUILLON" : titre === "Critères non remplis" ? "CRITERES_NON_REMPLIS" : null} />
              </div>
            ))}
          </div>
          {/* TABLEAU 
          <div className="board-admin-main-table">
            <table>
              <thead>
                <tr>
                  <th></th>
                  <th>Semaine concernée</th>
                  <th>Semaines précédentes</th>
                </tr>
              </thead>

              <tbody>
                {lignes.map((titre, index) => (
                  <tr key={index}>
                    <td>
                      <Button
                        onClick={() => {
                          setFiltre(titre);
                          setGroupe("Tous");
                        }}
                      >
                        {titre}
                      </Button>
                    </td>

                    <td>{getCount(titre)}</td>
                    <td>{getCountPast(titre)}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
                */}
          {/* LISTE */}
          <div className="board-admin-main-list">
            <div className="board-admin-main-list-header">
              <h3>{filtre}</h3>

              <p>{lastMondays[0]}</p>
              <p>{lastMondays[1]}</p>
              <p>{lastMondays[2]}</p>
              <p>{lastMondays[3]}</p>
            </div>
            <div className="board-admin-main-list-content">
              <ul>
                {dataFiltree.map((alt) => (
                  <li key={alt.alternantId}>
                    <Link
                      to={`/FicheAlternant/${alt.alternantId}`}
                      onClick={saveLocation}
                    >
                      {alt.nom} {alt.prenom}
                    </Link>

                    {alt.last4FichesAlternant.map((f, i) => (
                      <>
                      <div key={i} className="fiche-date">
                        <span key={i} className="fiche-badge">

                          {f.status}
                        </span>

                        <span>
                          <StatusIcons status={f.status} />
                        </span>
                      </div>
                      </>
                    ))}
                  </li>
                ))}
              </ul>
            </div>
            {/* FILTRE GROUPE */}
            <div className="board-admin-main-list-filter">
              <label>Groupe :</label>

              <select
                value={groupe}
                onChange={(e) => setGroupe(e.target.value)}
              >
                {groupes.map((g, index) => (
                  <option key={index} value={g}>
                    {g}
                  </option>
                ))}
              </select>
            </div>
          </div>
        </div>
      </div>
    </>
  );
}

export default BoardAdmin;
