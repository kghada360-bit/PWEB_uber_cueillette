<?php
require_once 'config.php';

if(!estAgriculteur()) {
    redirect('login.php');
}

$offre_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$sql_check = "SELECT * FROM offres WHERE id = :id AND agriculteur_id = :agriculteur_id";
$stmt = $conn->prepare($sql_check);
$stmt->execute([':id' => $offre_id, ':agriculteur_id' => $_SESSION['user_id']]);
$offre = $stmt->fetch();

if(!$offre) {
    redirect('mes_offres.php');
}

$sql = "SELECT c.*, o.nom, o.prenom, o.photo, o.description, 
        (SELECT AVG(note) FROM candidatures WHERE ouvrier_id = o.id AND note IS NOT NULL) as moyenne_notes
        FROM candidatures c 
        JOIN ouvriers o ON c.ouvrier_id = o.id 
        WHERE c.offre_id = :offre_id 
        ORDER BY c.date_candidature";
$stmt = $conn->prepare($sql);
$stmt->execute([':offre_id' => $offre_id]);
$candidatures = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Postulants - Uber-Cueillette</title>
    <link rel="stylesheet" href="style.css">
    <script src="script.js" defer></script>
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo">
                <h1>🌾 Uber-Cueillette</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php">Accueil</a></li>
                    <li><a href="dashboard_agriculteur.php">Tableau de bord</a></li>
                    <li><a href="mes_offres.php">Mes offres</a></li>
                    <li><a href="profil.php">Mon profil</a></li>
                    <li><a href="logout.php">Déconnexion</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="card">
            <h2>👥 Postulants pour l'offre : <?= htmlspecialchars($offre['type_fruit']) ?> - <?= htmlspecialchars($offre['gouvernorat']) ?></h2>
            <p>Places restantes : <?= $offre['nb_ouvriers_demandes'] - $offre['nb_ouvriers_acceptes'] ?></p>
            
            <?php if(count($candidatures) > 0): ?>
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Photo</th>
                                <th>Nom complet</th>
                                <th>Description</th>
                                <th>Note moyenne</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($candidatures as $candidat): ?>
                                <tr>
                                    <td>
                                        <?php if($candidat['photo'] && file_exists('uploads/' . $candidat['photo'])): ?>
                                            <img src="uploads/<?= $candidat['photo'] ?>" width="50" height="50" style="border-radius: 50%; object-fit: cover;">
                                        <?php else: ?>
                                            <div style="width: 50px; height: 50px; background: #ccc; border-radius: 50%; display: flex; align-items: center; justify-content: center;">📷</div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($candidat['prenom'] . ' ' . $candidat['nom']) ?></td>
                                    <td><?= htmlspecialchars(substr($candidat['description'], 0, 100)) ?>...</td>
                                    <td>
                                        <?php if($candidat['moyenne_notes']): ?>
                                            <span class="rating">⭐ <?= number_format($candidat['moyenne_notes'], 1) ?>/10</span>
                                        <?php else: ?>
                                            <span>Pas encore noté</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($candidat['statut'] == 'en_attente'): ?>
                                            <span class="badge badge-warning">En attente</span>
                                        <?php elseif($candidat['statut'] == 'acceptee'): ?>
                                            <span class="badge badge-success">Accepté</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">Refusé</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($candidat['statut'] == 'en_attente' && $offre['nb_ouvriers_acceptes'] < $offre['nb_ouvriers_demandes']): ?>
                                            <a href="gerer_candidature.php?id=<?= $candidat['id'] ?>&action=accepter&offre=<?= $offre_id ?>" class="btn btn-success btn-sm" onclick="return confirmerAction('Accepter ce candidat ?')">Accepter</a>
                                            <a href="gerer_candidature.php?id=<?= $candidat['id'] ?>&action=refuser&offre=<?= $offre_id ?>" class="btn btn-danger btn-sm" onclick="return confirmerAction('Refuser ce candidat ?')">Refuser</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>Aucun postulant pour cette offre pour le moment.</p>
            <?php endif; ?>
            
            <div style="margin-top: 1rem;">
                <a href="mes_offres.php" class="btn btn-primary">Retour à mes offres</a>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2025-2026 Uber-Cueillette</p>
    </footer>
</body>
</html>