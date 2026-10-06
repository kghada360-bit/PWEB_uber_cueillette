<?php
require_once 'config.php';

if(!estOuvrier()) {
    redirect('login.php');
}

$ouvrier_id = $_SESSION['user_id'];

$sql_stats = "SELECT 
    COUNT(*) as total_candidatures,
    SUM(CASE WHEN statut = 'en_attente' THEN 1 ELSE 0 END) as en_attente,
    SUM(CASE WHEN statut = 'acceptee' THEN 1 ELSE 0 END) as acceptees
    FROM candidatures WHERE ouvrier_id = :id";
$stmt = $conn->prepare($sql_stats);
$stmt->execute([':id' => $ouvrier_id]);
$stats = $stmt->fetch();

$sql_note = "SELECT AVG(note) as moyenne FROM candidatures WHERE ouvrier_id = :id AND note IS NOT NULL";
$stmt = $conn->prepare($sql_note);
$stmt->execute([':id' => $ouvrier_id]);
$note = $stmt->fetch();

$sql_offres = "SELECT o.*, a.nom, a.prenom 
               FROM offres o 
               JOIN agriculteurs a ON o.agriculteur_id = a.id 
               WHERE o.statut = 'ouverte' 
               AND o.date_limite_postulation >= CURDATE()
               AND o.nb_ouvriers_acceptes < o.nb_ouvriers_demandes
               ORDER BY o.date_creation DESC 
               LIMIT 5";
$stmt = $conn->prepare($sql_offres);
$stmt->execute();
$offres = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Ouvrier - Uber-Cueillette</title>
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
                    <li><a href="liste_offres.php">📋 Offres disponibles</a></li>
                    <li><a href="mes_candidatures.php">Mes candidatures</a></li>
                    <li><a href="mes_chantiers.php">Mes chantiers</a></li>
                    <li><a href="profil.php">👤 Mon profil</a></li>
                    <li><a href="logout.php">🚪 Déconnexion</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <h1>Bienvenue, <?= htmlspecialchars($_SESSION['user_pseudo']) ?> !</h1>
        
      
        <div class="row">
            <div class="col">
                <div class="card" style="text-align: center; background: linear-gradient(135deg, #2196f3, #1976d2); color: white;">
                    <div style="font-size: 2rem;">📝</div>
                    <h3 style="color: white;">Candidatures</h3>
                    <p style="font-size: 2rem; font-weight: bold;"><?= $stats['total_candidatures'] ?></p>
                </div>
            </div>
            <div class="col">
                <div class="card" style="text-align: center; background: linear-gradient(135deg, #ff9800, #f57c00); color: white;">
                    <div style="font-size: 2rem;">⏳</div>
                    <h3 style="color: white;">En attente</h3>
                    <p style="font-size: 2rem; font-weight: bold;"><?= $stats['en_attente'] ?></p>
                </div>
            </div>
            <div class="col">
                <div class="card" style="text-align: center; background: linear-gradient(135deg, #4caf50, #388e3c); color: white;">
                    <div style="font-size: 2rem;">⭐</div>
                    <h3 style="color: white;">Note moyenne</h3>
                    <p style="font-size: 2rem; font-weight: bold;"><?= number_format($note['moyenne'] ?? 0, 1) ?>/10</p>
                </div>
            </div>
        </div>
        
       
        <div class="card">
            <h2>📋 Dernières offres disponibles</h2>
            <?php if(count($offres) > 0): ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr><th>Agriculteur</th><th>Type de fruit</th><th>Gouvernorat</th><th>Période</th><th>Prix/jour</th><th>Places</th><th>Action</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach($offres as $offre): ?>
                                <tr>
                                    <td><?= htmlspecialchars($offre['prenom'] . ' ' . $offre['nom']) ?></td>
                                    <td><?= htmlspecialchars($offre['type_fruit']) ?></td>
                                    <td><?= htmlspecialchars($offre['gouvernorat']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($offre['date_debut'])) ?> - <?= date('d/m/Y', strtotime($offre['date_fin'])) ?></td>
                                    <td><?= $offre['prix_journee'] ?> DT</td>
                                    <td><?= $offre['nb_ouvriers_acceptes'] ?>/<?= $offre['nb_ouvriers_demandes'] ?></td>
                                    <td>
                                        <a href="postuler.php?id=<?= $offre['id'] ?>" class="btn btn-success btn-sm" onclick="return confirmerAction('Voulez-vous postuler à cette offre ?')">Postuler</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div style="margin-top: 1rem;">
                    <a href="liste_offres.php" class="btn btn-primary">Voir toutes les offres</a>
                </div>
            <?php else: ?>
                <p>Aucune offre disponible pour le moment.</p>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <p>&copy; 2025-2026 Uber-Cueillette</p>
    </footer>
</body>
</html>