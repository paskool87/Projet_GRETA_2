import { Routes, Route } from "react-router-dom";
import Home from "./pages/Home";
import Layout from "./components/Layout/Layout";

import TuteurProf from "./pages/TuteurProf";
import BoardAdmin from "./pages/BoardAdmin";
import PastWeeks from "./pages/PastWeeks";
/*import Logement from "./pages/Logement";*/
import Error from "./pages/Error";

import "./App.scss";

function App() {
  return (
      <Routes>
        <Route element={<Layout />}>
          <Route path="/" element={<Home />} />
          <Route path="/TuteurProf" element={<TuteurProf />} />
          <Route path="/BoardAdmin" element={<BoardAdmin />} />
          <Route path="/PastWeeks" element={<PastWeeks />} />
          {/*<Route path="/logement/:id" element={<Logement />} />*/}
          <Route path="*" element={<Error />} />
        </Route>
      </Routes>
  );
}
export default App;
