import logos from "../../assets/images/logosGreta.png";
import "./Header.scss";

function Header() {
  return (
    <>
      <div className="header">
        <img src={logos} alt="logos" className="header__logos" />
      </div>
    </>
  );
}

export default Header;
