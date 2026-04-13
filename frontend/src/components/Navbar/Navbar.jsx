import { useNavigate } from "react-router-dom";

import "./Navbar.scss";

function Navbar() {
  const navigate = useNavigate();

  const user = JSON.parse(localStorage.getItem("user"));

  const handleLogout = () => {
    const confirmLogout = window.confirm(
      "Voulez-vous vraiment vous déconnecter ?",
    );

    if (!confirmLogout) return;

    localStorage.removeItem("token");
    localStorage.removeItem("user");
    navigate("/");
  };

  return (
    <nav className="navbar">
      <span>
        Bonjour {user?.prenom} {user?.nom}
      </span>

      <button onClick={handleLogout}>Déconnexion</button>
    </nav>
  );
}

export default Navbar;
