import { useNavigate } from "react-router-dom";

import "./RetourButton.scss";

function RetourButton({ type, text }) {
  const navigate = useNavigate();

  const data = JSON.parse(localStorage.getItem("returnTo")) || {};
  const path = data[type];

  if (!path) return null;

  return (
    <div className="retour">
      <button onClick={() => navigate(path)} className="retour-button">
        {text}
      </button>
    </div>
  );
}

export default RetourButton;
