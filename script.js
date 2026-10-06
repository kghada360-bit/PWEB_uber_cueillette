
function validerInscriptionAgriculteur() {
    let valide = true;
    
    const nom = document.getElementById('nom');
    const prenom = document.getElementById('prenom');
    const nomRegex = /^[A-Za-z\u00C0-\u00FF\s]+$/;
    
    if (nom && !nomRegex.test(nom.value)) {
        afficherErreur('nom', 'Le nom ne doit contenir que des lettres');
        valide = false;
    }
    
    if (prenom && !nomRegex.test(prenom.value)) {
        afficherErreur('prenom', 'Le prénom ne doit contenir que des lettres');
        valide = false;
    }

    const cin = document.getElementById('cin');
    const cinRegex = /^[0-9]{8}$/;
    if (cin && !cinRegex.test(cin.value)) {
        afficherErreur('cin', 'Le CIN doit contenir exactement 8 chiffres');
        valide = false;
    }

    const email = document.getElementById('email');
    const emailRegex = /^[^\s@]+@([^\s@.,]+\.)+[^\s@.,]{2,}$/;
    if (email && !emailRegex.test(email.value)) {
        afficherErreur('email', 'Veuillez entrer un email valide');
        valide = false;
    }

    const pseudo = document.getElementById('pseudo');
    const pseudoRegex = /^[A-Za-z]+$/;
    if (pseudo && !pseudoRegex.test(pseudo.value)) {
        afficherErreur('pseudo', 'Le pseudo ne doit contenir que des lettres');
        valide = false;
    }

    const mdp = document.getElementById('mot_de_passe');
    const mdpRegex = /^[A-Za-z0-9]{7,}[$#]$/;
    if (mdp && !mdpRegex.test(mdp.value)) {
        afficherErreur('mot_de_passe', 'Le mot de passe doit contenir au moins 8 caractères (lettres/chiffres) et se terminer par $ ou #');
        valide = false;
    }

    const mdpConf = document.getElementById('confirmation_mdp');
    if (mdpConf && mdp.value !== mdpConf.value) {
        afficherErreur('confirmation_mdp', 'Les mots de passe ne correspondent pas');
        valide = false;
    }
    
    return valide;
}

function validerInscriptionOuvrier() {
    let valide = true;

    const nom = document.getElementById('nom');
    const prenom = document.getElementById('prenom');
    const nomRegex = /^[A-Za-z\u00C0-\u00FF\s]+$/;
    
    if (nom && !nomRegex.test(nom.value)) {
        afficherErreur('nom', 'Le nom ne doit contenir que des lettres');
        valide = false;
    }
    
    if (prenom && !nomRegex.test(prenom.value)) {
        afficherErreur('prenom', 'Le prénom ne doit contenir que des lettres');
        valide = false;
    }

    const cin = document.getElementById('cin');
    const cinRegex = /^[0-9]{8}$/;
    if (cin && !cinRegex.test(cin.value)) {
        afficherErreur('cin', 'Le CIN doit contenir exactement 8 chiffres');
        valide = false;
    }

    const email = document.getElementById('email');
    const emailRegex = /^[^\s@]+@([^\s@.,]+\.)+[^\s@.,]{2,}$/;
    if (email && !emailRegex.test(email.value)) {
        afficherErreur('email', 'Veuillez entrer un email valide');
        valide = false;
    }

    const pseudo = document.getElementById('pseudo');
    const pseudoRegex = /^[A-Za-z]+$/;
    if (pseudo && !pseudoRegex.test(pseudo.value)) {
        afficherErreur('pseudo', 'Le pseudo ne doit contenir que des lettres');
        valide = false;
    }

    const mdp = document.getElementById('mot_de_passe');
    const mdpRegex = /^[A-Za-z0-9]{7,}[$#]$/;
    if (mdp && !mdpRegex.test(mdp.value)) {
        afficherErreur('mot_de_passe', 'Le mot de passe doit contenir au moins 8 caractères (lettres/chiffres) et se terminer par $ ou #');
        valide = false;
    }

    const mdpConf = document.getElementById('confirmation_mdp');
    if (mdpConf && mdp.value !== mdpConf.value) {
        afficherErreur('confirmation_mdp', 'Les mots de passe ne correspondent pas');
        valide = false;
    }
 
    const photo = document.getElementById('photo');
    if (photo && photo.files.length > 0) {
        const fichier = photo.files[0];
        const typesValides = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
        if (!typesValides.includes(fichier.type)) {
            afficherErreur('photo', 'Veuillez choisir une image valide (JPEG, PNG, GIF)');
            valide = false;
        }
        if (fichier.size > 2 * 1024 * 1024) {
            afficherErreur('photo', 'La photo ne doit pas dépasser 2 Mo');
            valide = false;
        }
    }
    
    return valide;
}

function validerOffre() {
    let valide = true;
    
    const dateDebut = document.getElementById('date_debut');
    const dateFin = document.getElementById('date_fin');
    const dateLimite = document.getElementById('date_limite_postulation');
    const aujourdhui = new Date().toISOString().split('T')[0];
    
    if (dateDebut && dateDebut.value < aujourdhui) {
        afficherErreur('date_debut', 'La date de début ne peut pas être dans le passé');
        valide = false;
    }
    
    if (dateFin && dateFin.value <= dateDebut.value) {
        afficherErreur('date_fin', 'La date de fin doit être après la date de début');
        valide = false;
    }
    
    if (dateLimite && dateLimite.value >= dateDebut.value) {
        afficherErreur('date_limite_postulation', 'La date limite doit être avant la date de début');
        valide = false;
    }
    
    const nbOuvriers = document.getElementById('nb_ouvriers_demandes');
    if (nbOuvriers && (nbOuvriers.value < 1 || nbOuvriers.value > 100)) {
        afficherErreur('nb_ouvriers_demandes', 'Le nombre d\'ouvriers doit être entre 1 et 100');
        valide = false;
    }
    
    const prix = document.getElementById('prix_journee');
    if (prix && (prix.value < 10 || prix.value > 200)) {
        afficherErreur('prix_journee', 'Le prix doit être entre 10 et 200 DT');
        valide = false;
    }
    
    return valide;
}

function afficherErreur(champId, message) {
    const champ = document.getElementById(champId);
    if (champ) {
        champ.style.borderColor = '#f44336';
        let erreurDiv = champ.parentElement.querySelector('.erreur-message');
        if (!erreurDiv) {
            erreurDiv = document.createElement('div');
            erreurDiv.className = 'erreur-message';
            erreurDiv.style.color = '#f44336';
            erreurDiv.style.fontSize = '0.8rem';
            erreurDiv.style.marginTop = '0.3rem';
            champ.parentElement.appendChild(erreurDiv);
        }
        erreurDiv.textContent = message;

        setTimeout(() => {
            champ.style.borderColor = '#e0e0e0';
            if (erreurDiv) erreurDiv.remove();
        }, 3000);
    }
}

function filtrerOffres() {
    const typeFruit = document.getElementById('filter_fruit')?.value || '';
    const gouvernorat = document.getElementById('filter_gouv')?.value || '';
    const rows = document.querySelectorAll('#offres-table tbody tr');
    
    rows.forEach(row => {
        let afficher = true;
        
        if (typeFruit && !row.dataset.fruit?.includes(typeFruit)) {
            afficher = false;
        }
        if (gouvernorat && row.dataset.gouv !== gouvernorat) {
            afficher = false;
        }
        
        row.style.display = afficher ? '' : 'none';
    });
}

function trierParPrix() {
    const tbody = document.querySelector('#offres-table tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    
    rows.sort((a, b) => {
        const prixA = parseInt(a.dataset.prix) || 0;
        const prixB = parseInt(b.dataset.prix) || 0;
        return prixA - prixB;
    });
    
    rows.forEach(row => tbody.appendChild(row));
}

function confirmerAction(message) {
    return confirm(message);
}

document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(alert => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        });
    }, 5000);
    
    document.querySelectorAll('.card').forEach((card, index) => {
        card.style.animation = `fadeInUp 0.5s ease ${index * 0.1}s both`;
    });
});

const style = document.createElement('style');
style.textContent = `
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
`;
document.head.appendChild(style);