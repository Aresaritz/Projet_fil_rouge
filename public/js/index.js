fetch('http://localhost:3000/api/data')
.then(response => {
    // Vérifier si la réponse est OK
    if (!response.ok) {
        throw new Error('Erreur lors de la récupération des données');
    }
    return response.json(); // Convertir la réponse en JSON
})
.then(data => {
    // Afficher les données récupérées dans la page
    output.innerHTML = `<p>Message : ${data.message}</p><p>Données : ${data.data.join(', ')}</p>`;
})
.catch(error => {
    // Gérer les erreurs
    output.innerHTML = `<p>Erreur : ${error.message}</p>`;
});