<?php
require_once 'config.php';

if(!estAgriculteur()) {
    redirect('login.php');
}

$agriculteur_id = $_SESSION['user_id'];

$sql_stats = "SELECT 
    COUNT(*) as total_offres,
    SUM(CASE WHEN statut = 'ouverte' THEN 1 ELSE 0 END) as offres_ouvertes,
    SUM(CASE WHEN statut = 'cloturee' THEN 1 ELSE 0 END) as offres_cloturees,
    SUM(CASE WHEN statut = 'terminee' THEN 1 ELSE 0 END) as offres_terminees
    FROM offres WHERE agriculteur_id = :id";
$stmt = $conn->prepare($sql_stats);
$stmt->execute([':id' => $agriculteur_id]);
$stats = $stmt->fetch();

$sql_offres = "SELECT * FROM offres WHERE agriculteur_id = :id ORDER BY date_creation DESC LIMIT 5";
$stmt = $conn->prepare($sql_offres);
$stmt->execute([':id' => $agriculteur_id]);
$offres = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Agriculteur - Uber-Cueillette</title>
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
                    <li><a href="dashboard_agriculteur.php">Tableau de bord</a></li>
                    <li><a href="ajouter_offre.php">➕ Nouvelle offre</a></li>
                    <li><a href="mes_offres.php">Mes offres</a></li>
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
                    <div style="font-size: 2rem;">📊</div>
                    <h3 style="color: white;">Total offres</h3>
                    <p style="font-size: 2rem; font-weight: bold;"><?= $stats['total_offres'] ?></p>
                </div>
            </div>
            <div class="col">
                <div class="card" style="text-align: center; background: linear-gradient(135deg, #4caf50, #388e3c); color: white;">
                    <div style="font-size: 2rem;">🟢</div>
                    <h3 style="color: white;">Offres ouvertes</h3>
                    <p style="font-size: 2rem; font-weight: bold;"><?= $stats['offres_ouvertes'] ?></p>
                </div>
            </div>
            <div class="col">
                <div class="card" style="text-align: center; background: linear-gradient(135deg, #ff9800, #f57c00); color: white;">
                    <div style="font-size: 2rem;">🔴</div>
                    <h3 style="color: white;">Offres clôturées</h3>
                    <p style="font-size: 2rem; font-weight: bold;"><?= $stats['offres_cloturees'] + $stats['offres_terminees'] ?></p>
                </div>
            </div>
        </div>
        
        
        <div class="card">
            <h2>📋 Mes dernières offres</h2>
            <?php if(count($offres) > 0): ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr><th>Type de fruit</th><th>Gouvernorat</th><th>Période</th><th>Prix/jour</th><th>Places</th><th>Statut</th><th>Action</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach($offres as $offre): ?>
                                <tr>
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
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div style="margin-top: 1rem;">
                    <a href="mes_offres.php" class="btn btn-primary">Voir toutes mes offres</a>
                </div>
            <?php else: ?>
                <p>Vous n'avez pas encore créé d'offre.</p>
                <a href="ajouter_offre.php" class="btn btn-primary">Créer ma première offre</a>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <p>&copy; 2025-2026 Uber-Cueillette</p>
    </footer>
</body>
</html>