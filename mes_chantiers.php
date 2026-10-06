<?php
require_once 'config.php';

if(!estOuvrier()) {
    redirect('login.php');
}

$ouvrier_id = $_SESSION['user_id'];

$sql = "SELECT c.*, o.type_fruit, o.gouvernorat, o.date_debut, o.date_fin, o.prix_journee, a.nom as agriculteur_nom, a.prenom as agriculteur_prenom,
        DATEDIFF(o.date_fin, o.date_debut) + 1 as nb_jours
        FROM candidatures c 
        JOIN offres o ON c.offre_id = o.id 
        JOIN agriculteurs a ON o.agriculteur_id = a.id 
        WHERE c.ouvrier_id = :ouvrier_id AND (c.statut = 'acceptee' OR c.statut = 'terminee')
        ORDER BY o.date_debut DESC";
$stmt = $conn->prepare($sql);
$stmt->execute([':ouvrier_id' => $ouvrier_id]);
$chantiers = $stmt->fetchAll();

$total_gains = 0;
foreach($chantiers as $chantier) {
    if($chantier['remuneration_totale']) {
        $total_gains += $chantier['remuneration_totale'];
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes chantiers - Uber-Cueillette</title>
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
        <div class="card" style="background: linear-gradient(135deg, #2e7d32, #1b5e20); color: white; text-align: center;">
            <h2 style="color: white;">💰 Total des gains</h2>
            <p style="font-size: 2rem; font-weight: bold;"><?= number_format($total_gains, 2) ?> DT</p>
        </div>
        
        <div class="card">
            <h2>🏗️ Mes chantiers</h2>
            
            <?php if(count($chantiers) > 0): ?>
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Offre</th>
                                <th>Agriculteur</th>
                                <th>Gouvernorat</th>
                                <th>Période</th>
                                <th>Nb jours</th>
                                <th>Prix/jour</th>
                                <th>Rémunération</th>
                                <th>Note</th>
                                <th>Commentaire</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($chantiers as $chantier): ?>
                                <tr>
                                    <td><?= htmlspecialchars($chantier['type_fruit']) ?></td>
                                    <td><?= htmlspecialchars($chantier['agriculteur_prenom'] . ' ' . $chantier['agriculteur_nom']) ?></td>
                                    <td><?= htmlspecialchars($chantier['gouvernorat']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($chantier['date_debut'])) ?> - <?= date('d/m/Y', strtotime($chantier['date_fin'])) ?></td>
                                    <td><?= $chantier['nb_jours'] ?></td>
                                    <td><?= $chantier['prix_journee'] ?> DT</td>
                                    <td>
                                        <?php if($chantier['remuneration_totale']): ?>
                                            <?= number_format($chantier['remuneration_totale'], 2) ?> DT
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($chantier['note']): ?>
                                            <span class="rating">⭐ <?= $chantier['note'] ?>/10</span>
                                        <?php else: ?>
                                            En attente
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($chantier['commentaire'] ?? '-') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>Vous n'avez pas encore participé à un chantier.</p>
                <a href="liste_offres.php" class="btn btn-primary">Postuler à une offre</a>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <p>&copy; 2025-2026 Uber-Cueillette</p>
    </footer>
</body>
</html>