document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('.signup-form');

    form.addEventListener('submit', async (event) => {
        event.preventDefault(); // Empêche le rechargement de la page

        const formData = new FormData(form);
        const data = {
            email: formData.get('email'),
            password: formData.get('password'),
            nom: formData.get('lastName'),
            prenom: formData.get('firstName'),
        };

        try {
            const response = await fetch('index.php?page=inscrire', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data),
            });

            const result = await response.json();

            if (result.valide) {
                alert(result.message);
                window.location.href = 'index.php?page=connexion'; // Redirige vers la page de connexion
            } else {
                alert(result.message);
            }
        } catch (error) {
            console.error('Erreur lors de l\'envoi des données :', error);
            alert('Une erreur est survenue.');
        }
    });
});
