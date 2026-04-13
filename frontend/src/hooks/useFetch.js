import { useState, useEffect } from 'react';

function useFetch(url, storageKey) {
  const [data, setData] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    // Vérifier si les données existent déjà dans le localStorage
    const cachedData = localStorage.getItem(storageKey);
    
    if (cachedData) {
      // Si oui, utiliser les données du cache
      setData(JSON.parse(cachedData));
      setLoading(false);
      return; // Ne pas faire de fetch
    }

    // Sinon, faire le fetch
    const fetchData = async () => {
      setLoading(true);
      setError(null);

      try {
        // Récupérer le token
        const token = localStorage.getItem('token');

        // Faire la requête
        const response = await fetch(url, {
          method: 'GET',
          headers: {
            'Authorization': token ? `Bearer ${token}` : '',
            'Content-Type': 'application/json'
          }
        });

        // Vérifier si la réponse est OK
        if (!response.ok) {
          throw new Error(`Erreur HTTP: ${response.status}`);
        }

        // Récupérer les données
        const result = await response.json();

        // Sauvegarder dans le state
        setData(result);

        // Sauvegarder dans le localStorage
        localStorage.setItem(storageKey, JSON.stringify(result));

      } catch (err) {
        setError(err.message);
      } finally {
        setLoading(false);
      }
    };

    fetchData();

  }, [url, storageKey]); // Re-fetch si url ou storageKey change

  return { data, loading, error };
}

export default useFetch;