import { useEffect } from "react";
import { useNavigate } from "react-router-dom";
import useFetch from "../../hooks/useFetch";
import { BASE_URL } from "../../config";

import Navbar from "../../components/Navbar/Navbar";


function AlternantToFiche() {
  const navigate = useNavigate();

  const user = JSON.parse(localStorage.getItem("user"));

  const { data, loading, error } = useFetch(
    `${BASE_URL}/api/fiche`,
    "cache_api"
  );

  useEffect(() => {
    if (loading || error || !data) return;

    const idAlternant = user?.id_alternant;

    if (idAlternant) {
      navigate(`/FicheAlternant/${idAlternant}`);
    } else {
      navigate("/");
    }
  }, [data, loading, error, user, navigate]);

  if (loading) return (
    <>
      <Navbar />
      <p>Chargement...</p>
    </>
  );
  if (error) return (
    <>
      <Navbar />
      <p>Erreur : {error.message || error}</p>
    </>
  );

  return (
    <>
      <Navbar />
      <p>Redirection...</p>
    </>
  );
}

export default AlternantToFiche;