const FakeUsers = [
  {
    email: "admin@test.com",
    password: "1234",
    token: "token-admin",
    prenom: "Alice",
    nom: "Admin",
    role: "administrateur",
  },

  // 👇 PROFESSEURS
  {
    email: "prof1@test.com",
    password: "1234",
    token: "token-prof1",
    prenom: "Pierre",
    nom: "prof1",
    role: "professeur",
  },
  {
    email: "prof2@test.com",
    password: "1234",
    token: "token-prof2",
    prenom: "Paul",
    nom: "prof2",
    role: "professeur",
  },

  // 👇 TUTEURS
  {
    email: "tuteur1@test.com",
    password: "1234",
    token: "token-tuteur1",
    prenom: "Thierry",
    nom: "tuteur1",
    role: "tuteur",
  },
  {
    email: "tuteur2@test.com",
    password: "1234",
    token: "token-tuteur2",
    prenom: "Thomas",
    nom: "tuteur2",
    role: "tuteur",
  },

  // 👇 AUTRES
 { email: "alt1@test.com", password: "1234", token: "token-alt-1", prenom: "Jean", nom: "Dupont", role: "alternant", alternantId: 1 },
  { email: "alt2@test.com", password: "1234", token: "token-alt-2", prenom: "Sophie", nom: "Martin", role: "alternant", alternantId: 2 },
  { email: "alt3@test.com", password: "1234", token: "token-alt-3", prenom: "Paul", nom: "Durand", role: "alternant", alternantId: 3 },
  { email: "alt4@test.com", password: "1234", token: "token-alt-4", prenom: "Claire", nom: "Petit", role: "alternant", alternantId: 4 },
  { email: "alt5@test.com", password: "1234", token: "token-alt-5", prenom: "Lucas", nom: "Moreau", role: "alternant", alternantId: 5 },
  { email: "alt6@test.com", password: "1234", token: "token-alt-6", prenom: "Emma", nom: "Simon", role: "alternant", alternantId: 6 },
  { email: "alt7@test.com", password: "1234", token: "token-alt-7", prenom: "Hugo", nom: "Laurent", role: "alternant", alternantId: 7 },
  { email: "alt8@test.com", password: "1234", token: "token-alt-8", prenom: "Chloe", nom: "Lefebvre", role: "alternant", alternantId: 8 },
  { email: "alt9@test.com", password: "1234", token: "token-alt-9", prenom: "Nathan", nom: "Michel", role: "alternant", alternantId: 9 },
  { email: "alt10@test.com", password: "1234", token: "token-alt-10", prenom: "Lea", nom: "Garcia", role: "alternant", alternantId: 10 },

  { email: "alt11@test.com", password: "1234", token: "token-alt-11", prenom: "Tom", nom: "Roux", role: "alternant", alternantId: 11 },
  { email: "alt12@test.com", password: "1234", token: "token-alt-12", prenom: "Anna", nom: "Vincent", role: "alternant", alternantId: 12 },
  { email: "alt13@test.com", password: "1234", token: "token-alt-13", prenom: "Leo", nom: "Fournier", role: "alternant", alternantId: 13 },
  { email: "alt14@test.com", password: "1234", token: "token-alt-14", prenom: "Ines", nom: "Morel", role: "alternant", alternantId: 14 },
  { email: "alt15@test.com", password: "1234", token: "token-alt-15", prenom: "Noah", nom: "Girard", role: "alternant", alternantId: 15 },
  { email: "alt16@test.com", password: "1234", token: "token-alt-16", prenom: "Manon", nom: "Andre", role: "alternant", alternantId: 16 },
  { email: "alt17@test.com", password: "1234", token: "token-alt-17", prenom: "Ethan", nom: "Mercier", role: "alternant", alternantId: 17 },
  { email: "alt18@test.com", password: "1234", token: "token-alt-18", prenom: "Jade", nom: "Dupuis", role: "alternant", alternantId: 18 },
  { email: "alt19@test.com", password: "1234", token: "token-alt-19", prenom: "Adam", nom: "Lambert", role: "alternant", alternantId: 19 },
  { email: "alt20@test.com", password: "1234", token: "token-alt-20", prenom: "Sarah", nom: "Bonnet", role: "alternant", alternantId: 20 }];

export default FakeUsers;