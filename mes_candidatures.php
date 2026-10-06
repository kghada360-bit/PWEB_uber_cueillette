<?php
require_once 'config.php';

if(!estOuvrier()) {
    redirect('login.php');
}

$ouvrier_id = $_SESSION['user_id'];

$sql = "SELECT c.*, o.type_fruit, o.gouvernorat, o.date_debut, o.date_fin, o.prix_journee, a.nom as agriculteur_nom, a.prenom as agriculteur_prenom
        FROM candidatures c 
        JOIN offres o ON c.offre_id = o.id 
        JOIN agriculteurs a ON o.agriculteur_id = a.id 
        WHERE c.ouvrier_id = :ouvrier_id 
        ORDER BY c.date_candidature DESC";
$stmt = $conn->prepare($sql);
$stmt->execute([':ouvrier_id' => $ouvrier_id]);
$candidatures = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes candidatures - Uber-Cueillette</title>
    <link rel="stylesheet" href="style.css">
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
                    <li><a href="dashboard_ouvrier.php">Tableau de bord</a></li>
                    <li><a href="liste_offres.php">Offres disponibles</a></li>
                    <li><a href="mes_candidatures.php">Mes candidatures</a></li>
                    <li><a href="mes_chantiers.php">Mes chantiers</a></li>
                    <li><a href="profil.php">Mon profil</a></li>
                    <li><a href="logout.php">Déconnexion</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="card">
            <h2>📝 Mes candidatures</h2>
            
            <?php if(count($candidatures) > 0): ?>
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Offre</th>
                                <th>Agriculteur</th>
                                <th>Gouvernorat</th>
                                <th>Période</th>
                                <th>Prix/jour</th>
                                <th>Statut</th>
                                <th>Date candidature</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($candidatures as $candidature): ?>
                                <tr>
                                    <td><?= htmlspecialchars($candidature['type_fruit']) ?></td>
                                    <td><?= htmlspecialchars($candidature['agriculteur_prenom'] . ' ' . $candidature['agriculteur_nom']) ?></td>
                                    <td><?= htmlspecialchars($candidature['gouvernorat']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($candidature['date_debut'])) ?> - <?= date('d/m/Y', strtotime($candidature['date_fin'])) ?></td>
                                    <td><?= $candidature['prix_journee'] ?> DT</td>
                                    <td>
                                        <?php if($candidature['statut'] == 'en_attente'): ?>
                                            <span class="badge badge-warning">En attente</span>
                                        <?php elseif($candidature['statut'] == 'acceptee'): ?>
                                            <span class="badge badge-success">Acceptée</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">Refusée</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= date('d/m/Y H:i', strtotime($candidature['date_candidature'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>Vous n'avez pas encore postulé à une offre.</p>
                <a href="liste_offres.php" class="btn btn-primary">Voir les offres disponibles</a>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <p>&copy; 2025-2026 Uber-Cueillette</p>
    </footer>
</body>
</html>