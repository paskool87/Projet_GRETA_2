import { Routes, Route } from "react-router-dom";
import Layout from "./components/Layout/Layout";

import PrivateRoute from "./PrivateRoute";
import Home from "./pages/Home";
import TuteurProf from "./pages/TuteurProf";
import BoardAdmin from "./pages/BoardAdmin";
import Inscriptions from "./pages/Inscriptions";
import PastWeeks from "./pages/PastWeeks";
import FicheAlternant from "./pages/FicheAlternant";
import AlternantToFiche from "./pages/AlternantToFiche";
import Fiches from "./pages/Fiches";
import Error from "./pages/Error";

import "./App.scss";

function App() {
  return (
    <Routes>
      <Route element={<Layout />}>
        <Route path="/" element={<Home />} />
        <Route element={<PrivateRoute />}>
          <Route path="/TuteurProf" element={<TuteurProf />} />
          <Route path="/BoardAdmin" element={<BoardAdmin />} />
          <Route path="/Inscriptions" element={<Inscriptions />} />
          <Route path="/PastWeeks/:alternantId" element={<PastWeeks/>} />
          
          <Route path="/AlternantToFiche" element={<AlternantToFiche />} />
          <Route path="/FicheAlternant/:alternantId" element={<FicheAlternant />} />
          <Route path="/Fiches/:alternantId/:ficheId" element={<Fiches />} />
          

        </Route>
        <Route path="*" element={<Error />} />
      </Route>
    </Routes>
  );
}
export default App;
