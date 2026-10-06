<?php
require_once 'config.php';

if(!estAgriculteur()) {
    redirect('login.php');
}

$agriculteur_id = $_SESSION['user_id'];

$sql = "SELECT * FROM offres WHERE agriculteur_id = :id ORDER BY date_creation DESC";
$stmt = $conn->prepare($sql);
$stmt->execute([':id' => $agriculteur_id]);
$offres = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes offres - Uber-Cueillette</title>
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
                    <li><a href="ajouter_offre.php">Nouvelle offre</a></li>
                    <li><a href="mes_offres.php">Mes offres</a></li>
                    <li><a href="profil.php">Mon profil</a></li>
                    <li><a href="logout.php">Déconnexion</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="card">
            <h2>📋 Mes offres de récolte</h2>
            
            <?php if(count($offres) > 0): ?>
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Type de fruit</th>
                                <th>Gouvernorat</th>
                                <th>Période</th>
                                <th>Prix/jour</th>
                                <th>Places</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($offres as $offre): ?>
                                <tr>
                                    <td><?= $offre['id'] ?></td>
                                    <td><?= htmlspecialchars($offre['type_fruit']) ?></td>
                                    <td><?= htmlspecialchars($offre['gouvernorat']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($offre['date_debut'])) ?> - <?= date('d/m/Y', strtotime($offre['date_fin'])) ?></td>
                                    <td><?= $offre['prix_journee'] ?> DT</td>
                                    <td><?= $offre['nb_ouvriers_acceptes'] ?>/<?= $offre['nb_ouvriers_demandes'] ?></td>
                                    <td>
                                        <?php if($offre['statut'] == 'ouverte'): ?>
                                            <span class="badge badge-success">Ouverte</span>
                                        <?php elseif($offre['statut'] == 'cloturee'): ?>
                                            <span class="badge badge-warning">Clôturée</span>
                                        <?php else: ?>
                                            <span class="badge badge-info">Terminée</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="voir_postulants.php?id=<?= $offre['id'] ?>" class="btn btn-info btn-sm">Voir postulants</a>
                                        <?php if($offre['statut'] == 'terminee'): ?>
                                            <a href="noter_ouvrier.php?id=<?= $offre['id'] ?>" class="btn btn-secondary btn-sm">Noter</a>
                                        <?php endif; ?>
                                        <?php if($offre['statut'] == 'ouverte' && $offre['nb_ouvriers_acceptes'] == 0): ?>
                                            <a href="supprimer_offre.php?id=<?= $offre['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirmerAction('Supprimer cette offre ?')">Supprimer</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>Vous n'avez pas encore créé d'offre.</p>
                <a href="ajouter_offre.php" class="btn btn-primary">Créer une offre</a>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <p>&copy; 2025-2026 Uber-Cueillette</p>
    </footer>
</body>
</html>