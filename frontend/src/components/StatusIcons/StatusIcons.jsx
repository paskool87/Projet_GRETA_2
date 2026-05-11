import brouillonIcon from "../../assets/images/panneau_attention.png";
import soumiseIcon from "../../assets/images/panneau_orange.png";
import valideIcon from "../../assets/images/pouce_vert.png";
import criteresIcon from "../../assets/images/pouce_vers_bas.png";

import "./StatusIcons.scss";


function StatusIcons({ status }) {
  const statusIcons = {
    BROUILLON: brouillonIcon,
    SOUMISE: soumiseIcon,
    VALIDE: valideIcon,
    CRITERES_NON_REMPLIS: criteresIcon,
  };
  const icon = statusIcons[status];
  return icon ? <img src={icon} alt={status} className="fiche-icon" /> : null;
}

export default StatusIcons;