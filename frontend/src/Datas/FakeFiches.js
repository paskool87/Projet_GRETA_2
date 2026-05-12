import FakeAlternants from "./FakeAlternants";


const FakeFiches = [];

const statuts = [
  "soumise",
  "validation approuvée",
  "criteres non remplis",
  "non soumise",
];

// 5 lundis qui se suivent
const lundis = [
  "2026-03-23",
  "2026-03-30",
  "2026-04-06",
  "2026-04-13",
  "2026-04-20",
];

let ficheId = 1;

FakeAlternants.forEach((alternant) => {
  lundis.forEach((date, index) => {
    FakeFiches.push({
      id: ficheId++,
      alternantId: alternant.id,
      nom: alternant.nom,
      prenom: alternant.prenom,
      date: date,
      statut: statuts[(alternant.id + index) % statuts.length],
    });
  });
});

export default FakeFiches;