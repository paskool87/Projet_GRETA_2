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
      //slice (0,-2) pour correspondre à la semaine dernière
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

      pastNotValidatedCount: pastFichesAlternants.filter(
        (f) => f.status !== "VALIDE",
      ).length,
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
    ...[
      ...new Set(
        listeAlternants.map((a) => a.formationId + " - " + a.formation),
      ),
    ].sort((a, b) => {
      const idA = parseInt(a.split(" - ")[0], 10);
      const idB = parseInt(b.split(" - ")[0], 10);

      return idA - idB;
    }),
  ];
  
  const getCount = (titre) => {
    return listeAlternants.filter(mapping[titre]).length;
  };
  const getCountPast = (titre) => {
    return listeAlternants
      .flatMap((a) => a.pastFichesAlternant)
      .filter(mapping[titre]).length;
  };

  const dataFiltreeTotal = listeAlternants.filter((item) => {
    const matchStatut = mapping["Total alternants"](item);
    const matchGroupe =
      groupe === "Tous" ||
      parseInt(item.formationId, 10) === parseInt(groupe, 10);
    return matchStatut && matchGroupe;
  });

  const dataFiltreeAttente = listeAlternants.filter((item) => {
    const matchStatut = mapping["En attente de validation"](item);
    const matchGroupe =
      groupe === "Tous" ||
      parseInt(item.formationId, 10) === parseInt(groupe, 10);
    return matchStatut && matchGroupe;
  });
  const dataFiltreeFichesNonRemplies = listeAlternants.filter((item) => {
    const matchStatut = mapping["Fiches non remplies"](item);
    const matchGroupe =
      groupe === "Tous" ||
      parseInt(item.formationId, 10) === parseInt(groupe, 10);
    return matchStatut && matchGroupe;
  });
  const dataFiltreeCriteres = listeAlternants.filter((item) => {
    const matchStatut = mapping["Critères non remplis"](item);
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
          {/* vue globale semaine précédente(semmaine à valider) */}
          <div className="board-admin-main-global">
            <GetLastWeek />

            {lignes.map((titre, index) => (
              <div key={index} className="board-admin-main-global-item">
                <p>{getCount(titre)}</p>
                <div className="board-admin-main-global-item-box">
                  <h3>{titre}</h3>
                  <StatusIcons
                    status={
                      titre === "Fiche validée"
                        ? "VALIDE"
                        : titre === "En attente de validation"
                        ? "SOUMISE"
                        : titre === "Fiches non remplies"
                        ? "BROUILLON"
                        : titre === "Critères non remplis"
                        ? "CRITERES_NON_REMPLIS"
                        : null
                    }
                  />
                </div>
              </div>
            ))}
          </div>

          {/* LISTES */}

          <div className="board-admin-main-lists">
            <div className="board-admin-main-lists-list">
              <div className="board-admin-main-lists-list-header">
                <h3>Total alternants</h3>

                <p>
                  Semaine du<br></br>
                  {lastMondays[0]}
                </p>
                <p>
                  Semaine du<br></br>
                  {lastMondays[1]}
                </p>
                <p>
                  Semaine du<br></br>
                  {lastMondays[2]}
                </p>
                <p>
                  Semaine du<br></br>
                  {lastMondays[3]}
                </p>
                <p>Fiches précédentes non conformes </p>
              </div>
              <div className="board-admin-main-lists-list-content">
                <ul>
                  {dataFiltreeTotal.map((alt) => (
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
                      <span
                        className={
                          //remettre 0 et 5 (ou 2)
                          `number-badge
                          ${alt.pastNotValidatedCount === 10 ? "success" : ""}
                          ${alt.pastNotValidatedCount > 15 ? "danger" : ""}
                   `
                        }
                      >
                        {alt.pastNotValidatedCount}
                      </span>
                    </li>
                  ))}
                </ul>
              </div>
            </div>

            <div className="board-admin-main-lists-list second-list">
              <div className="board-admin-main-lists-list-header">
                <h3>En attente de validation</h3>

                <p>
                  Semaine du<br></br>
                  {lastMondays[0]}
                </p>
              </div>
              <div className="board-admin-main-lists-list-content">
                <ul>
                  {dataFiltreeAttente.map((alt) => (
                    <li key={alt.alternantId}>
                      <Link
                        to={`/FicheAlternant/${alt.alternantId}`}
                        onClick={saveLocation}
                      >
                        {alt.nom} {alt.prenom}
                      </Link>

                      <>
                        <div className="fiche-date">
                          <span className="fiche-badge">
                            {alt.last4FichesAlternant[0]?.status || "N/A"}
                          </span>

                          <span>
                            <StatusIcons
                              status={alt.last4FichesAlternant[0]?.status}
                            />
                          </span>
                        </div>
                      </>
                    </li>
                  ))}
                </ul>
              </div>
            </div>

            <div className="board-admin-main-lists-list second-list">
              <div className="board-admin-main-lists-list-header">
                <h3> Fiches non remplies </h3>

                <p>
                  Semaine du<br></br>
                  {lastMondays[0]}
                </p>
              </div>
              <div className="board-admin-main-lists-list-content">
                <ul>
                  {dataFiltreeFichesNonRemplies.map((alt) => (
                    <li key={alt.alternantId}>
                      <Link
                        to={`/FicheAlternant/${alt.alternantId}`}
                        onClick={saveLocation}
                      >
                        {alt.nom} {alt.prenom}
                      </Link>

                      <>
                        <div className="fiche-date">
                          <span className="fiche-badge">
                            {alt.last4FichesAlternant[0]?.status || "N/A"}
                          </span>

                          <span>
                            <StatusIcons
                              status={alt.last4FichesAlternant[0]?.status}
                            />
                          </span>
                        </div>
                      </>
                    </li>
                  ))}
                </ul>
              </div>
            </div>
            <div className="board-admin-main-lists-list second-list">
              <div className="board-admin-main-lists-list-header">
                <h3> Critères non remplis </h3>

                <p>
                  Semaine du<br></br>
                  {lastMondays[0]}
                </p>
              </div>
              <div className="board-admin-main-lists-list-content">
                <ul>
                  {dataFiltreeCriteres.map((alt) => (
                    <li key={alt.alternantId}>
                      <Link
                        to={`/FicheAlternant/${alt.alternantId}`}
                        onClick={saveLocation}
                      >
                        {alt.nom} {alt.prenom}
                      </Link>

                      <>
                        <div className="fiche-date">
                          <span className="fiche-badge">
                            {alt.last4FichesAlternant[0]?.status || "N/A"}
                          </span>

                          <span>
                            <StatusIcons
                              status={alt.last4FichesAlternant[0]?.status}
                            />
                          </span>
                        </div>
                      </>
                    </li>
                  ))}
                </ul>
              </div>
            </div>
          </div>

          {/* FILTRE GROUPE X2 */}
          <div className="board-admin-main-filter">
            <label>Groupe :</label>

            <select value={groupe} onChange={(e) => setGroupe(e.target.value)}>
              {groupes.map((g, index) => (
                <option key={index} value={g}>
                  {g}
                </option>
              ))}
            </select>
          </div>

          <div className="board-admin-main-filter second-filter">
            <label>Groupe :</label>

            <select value={groupe} onChange={(e) => setGroupe(e.target.value)}>
              {groupes.map((g, index) => (
                <option key={index} value={g}>
                  {g}
                </option>
              ))}
            </select>
          </div>
        </div>
      </div>
    </>
  );
}

export default BoardAdmin;
