import { useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";

import { BASE_URL } from "../../config";
import usePost from "../../hooks/usePost";

import PrincipalTitle from "../../components/PrincipalTitle/PrincipalTitle";
import Input from "../../components/Input/Input";
import Button from "../../components/Button/Button";
import ButtonDisabled from "../../components/ButtonDisabled/ButtonDisabled";

import "./Home.scss";

function Home() {
  useEffect(() => {
    localStorage.removeItem("token");
    localStorage.removeItem("user");
    localStorage.removeItem("returnTo");
    localStorage.removeItem("cache_alternants_boardAdmin");
    localStorage.removeItem("cache_alternants_tuteurProf");
    localStorage.removeItem("cache_alternants_alternants");
    localStorage.removeItem("cache_alternants_pastWeeks");
    localStorage.removeItem("cache_fiches_pastWeeks");
    localStorage.removeItem("cache_api");

  }, []);

  const [formData, setFormData] = useState({
    username: "",
    password: "",
  });

  const navigate = useNavigate();

  const { postData, loading, error } = usePost();

  function handleChange(e) {
    const { name, value } = e.target;

    setFormData((prev) => ({
      ...prev,
      [name]: value,
    }));
  }

  async function handleSubmit(e) {
    e.preventDefault();

    try {
      const data = await postData(
        `${BASE_URL}/api/login_check`,
        formData
      );

      // token
      localStorage.setItem("token", data.token);

      const rolesPriority = [
        "ADMINISTRATEUR",
        "TUTEUR",
        "PROFESSEUR_REFERENT",
        "ALTERNANT",
      ];

      const user = data.user;
      const role = rolesPriority.find((r) =>
        user.roles.includes(r)
      );

      localStorage.setItem(
        "user",
        JSON.stringify({
          prenom: user.prenom,
          nom: user.nom,
          id_utilisateur: user.id_utilisateur,
          role: role,
          id_alternant: user.id_alternant
        })
      );

      const roleRoutes = {
        ADMINISTRATEUR: "/BoardAdmin",
        ALTERNANT: "/AlternantToFiche",
        TUTEUR: "/TuteurProf",
        PROFESSEUR_REFERENT: "/TuteurProf",
      };

      navigate(roleRoutes[role] || "/");
    } catch (err) {
      console.error("Erreur login :", err.message);
    }
  }

  return (
    <div className="home">
      <PrincipalTitle />

      <form className="home-form" onSubmit={handleSubmit}>
        <Input
          label="Email"
          type="email"
          name="username"
          value={formData.username}
          onChange={handleChange}
        />

        <Input
          label="Mot de passe"
          type="password"
          name="password"
          value={formData.password}
          onChange={handleChange}
        />

        {loading ? (
          <ButtonDisabled>Connexion...</ButtonDisabled>
        ) : (
          <Button type="submit">Connexion</Button>
        )}
      </form>

      {error && <div className="error-message">{error}</div>}

      {loading && (
        <div className="waiting-message">
          Vérification des identifiants...
        </div>
      )}
    </div>
  );
}

export default Home;