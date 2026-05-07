// Générateur de faux dataset pour BoardAdmin
// Parce qu'écrire 100 alternants à la main est une punition médiévale.

const statuts = [
  "BROUILLON",
  "SOUMISE",
  "VALIDE",
  "CRITERES_NON_REMPLIS",
];

const validateurs = [null, "formation", "entreprise"];

const formations = [
  {
    id: 1,
    nom_formation: "Formation DWWM",
    description: "Développeur Web et Web Mobile",
  },
  {
    id: 2,
    nom_formation: "Formation CDA",
    description: "Concepteur Développeur d’Applications",
  },
  {
    id: 3,
    nom_formation: "Formation DevOps",
    description: "Administration systèmes et cloud",
  },
  {
    id: 4,
    nom_formation: "Formation Cybersécurité",
    description: "Sécurité informatique",
  },
  {
    id: 5,
    nom_formation: "Formation Data",
    description: "Data analyst et IA",
  },
];

const noms = [
  "Martin",
  "Bernard",
  "Thomas",
  "Petit",
  "Robert",
  "Richard",
  "Durand",
  "Dubois",
  "Moreau",
  "Laurent",
  "Simon",
  "Michel",
  "Lefebvre",
  "Leroy",
  "Roux",
  "David",
  "Bertrand",
  "Morel",
  "Fournier",
  "Girard",
];

const prenoms = [
  "Lucas",
  "Emma",
  "Nathan",
  "Jade",
  "Louis",
  "Chloé",
  "Hugo",
  "Lina",
  "Enzo",
  "Manon",
  "Tom",
  "Camille",
  "Léo",
  "Sarah",
  "Noah",
  "Eva",
  "Arthur",
  "Clara",
  "Gabriel",
  "Inès",
];

function randomItem(array) {
  return array[Math.floor(Math.random() * array.length)];
}

function formatDate(date) {
  return date.toISOString().split(".")[0] + "+01:00";
}

function addDays(date, days) {
  const result = new Date(date);
  result.setDate(result.getDate() + days);
  return result;
}

function generateFiches(alternantId, startId) {
  const fiches = [];

  const startDate = new Date("2026-01-05T00:00:00");

  const nombreSemaines = 16 + Math.floor(Math.random() * 12);

  for (let i = 0; i < nombreSemaines; i++) {
    const debut = addDays(startDate, i * 7);
    const fin = addDays(debut, 4);

    const status = randomItem(statuts);

    let dateSoumission = null;
    let dateValidation = null;
    let validateur = null;

    if (status !== "BROUILLON") {
      dateSoumission = formatDate(addDays(fin, 0));
      validateur = randomItem(["formation", "entreprise"]);
    }

    if (status === "VALIDE") {
      dateValidation = formatDate(addDays(fin, 1));
    }

    fiches.push({
      id: startId + i,
      validateur,
      date_creation: formatDate(debut),
      date_debut: formatDate(debut),
      date_fin: formatDate(fin),
      date_soumission: dateSoumission,
      date_validation: dateValidation,
      status_fiche: status,
    });
  }

  return fiches;
}

const alternants = [];

let ficheId = 1;

for (let i = 1; i <= 100; i++) {
  const nom = randomItem(noms);
  const prenom = randomItem(prenoms);

  const formation = randomItem(formations);

  const fiches = generateFiches(i, ficheId);

  ficheId += fiches.length;

  alternants.push({
    alternant: {
      id: i,
      actif: Math.random() > 0.1,
      tutorats: [[]],
      utilisateur: {
        id: i + 1000,
        nom,
        prenom,
        email: `${prenom.toLowerCase()}.${nom.toLowerCase()}${i}@gmail.com`,
        role: "ALTERNANT",
        date_creation: "2026-01-01T00:00:00+01:00",
        actif: true,
      },
    },

    formation,

    fiche: fiches,
  });
}

export default alternants;

// Exemple d'utilisation :
// import alternants from './mockAlternants';
// console.log(alternants);
// console.log(alternants[0].fiche);

// Tu as maintenant un dataset suffisamment gros pour :
// - tester tes filtres
// - casser ton render React
// - découvrir que key={index} était une mauvaise idée
// - pleurer sur les performances
// Le cycle naturel du frontend moderne.
