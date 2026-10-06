<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uber-Cueillette - Mise en relation agriculteurs et ouvriers</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo">
                <h1>🌾 Uber-Cueillette</h1>
                <p>La récolte n'a jamais été aussi simple !</p>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php">Accueil</a></li>
                    <?php if(estConnecte()): ?>
                        <?php if(estAgriculteur()): ?>
                            <li><a href="dashboard_agriculteur.php">Tableau de bord</a></li>
                            <li><a href="ajouter_offre.php">➕ Nouvelle offre</a></li>
                            <li><a href="mes_offres.php">Mes offres</a></li>
                        <?php elseif(estOuvrier()): ?>
                            <li><a href="dashboard_ouvrier.php">Tableau de bord</a></li>
                            <li><a href="liste_offres.php">📋 Offres disponibles</a></li>
                            <li><a href="mes_candidatures.php">Mes candidatures</a></li>
                            <li><a href="mes_chantiers.php">Mes chantiers</a></li>
                        <?php endif; ?>
                        <li><a href="profil.php">👤 Mon profil</a></li>
                        <li><a href="logout.php">🚪 Déconnexion</a></li>
                    <?php else: ?>
                        <li><a href="login.php">Connexion</a></li>
                        <li><a href="inscription_agriculteur.php">👨‍🌾 Inscription Agriculteur</a></li>
                        <li><a href="inscription_ouvrier.php">👨‍🌾 Inscription Ouvrier</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="card" style="text-align: center; background: linear-gradient(135deg, #2e7d32, #1b5e20); color: white;">
            <h1 style="color: white; border-bottom-color: #ff9800;">Bienvenue sur Uber-Cueillette</h1>
            <p style="font-size: 1.2rem; margin-top: 1rem;">La plateforme qui connecte agriculteurs et ouvriers pour des récoltes réussies !</p>
            <div style="margin-top: 2rem;">
                <?php if(!estConnecte()): ?>
                    <a href="inscription_agriculteur.php" class="btn btn-primary" style="margin-right: 1rem;">Je suis agriculteur</a>
                    <a href="inscription_ouvrier.php" class="btn btn-secondary">Je suis ouvrier</a>
                <?php endif; ?>
            </div>
        </div>

        <div class="row" style="margin-top: 2rem;">
            <div class="col">
                <div class="card" style="text-align: center;">
                    <div style="font-size: 3rem;">👨‍🌾</div>
                    <h3>Pour les Agriculteurs</h3>
                    <p>Publiez vos offres de récolte, trouvez des ouvriers qualifiés, et gérez vos chantiers facilement.</p>
                </div>
            </div>
            <div class="col">
                <div class="card" style="text-align: center;">
                    <div style="font-size: 3rem;">👨‍🌾</div>
                    <h3>Pour les Ouvriers</h3>
                    <p>Trouvez des missions de récolte, postulez en un clic, et gagnez de l'argent.</p>
                </div>
            </div>
            <div class="col">
                <div class="card" style="text-align: center;">
                    <div style="font-size: 3rem;">⭐</div>
                    <h3>Système de notation</h3>
                    <p>Notez et commentez les ouvriers pour garantir la qualité des services.</p>
                </div>
            </div>
        </div>

        <?php if(!estConnecte() || estOuvrier()): ?>
        <div class="card">
            <h2>📋 Dernières offres disponibles</h2>
            <?php
            $sql = "SELECT o.*, a.nom, a.prenom 
                    FROM offres o 
                    JOIN agriculteurs a ON o.agriculteur_id = a.id 
                    WHERE o.statut = 'ouverte' 
                    AND o.date_limite_postulation >= CURDATE()
                    AND o.nb_ouvriers_acceptes < o.nb_ouvriers_demandes
                    ORDER BY o.date_creation DESC 
                    LIMIT 5";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $offres = $stmt->fetchAll();
            ?>
            
            <?php if(count($offres) > 0): ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr><th>Agriculteur</th><th>Type de fruit</th><th>Gouvernorat</th><th>Période</th><th>Prix/jour</th><th>Places</th></tr>
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
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php if(estOuvrier()): ?>
                    <div style="margin-top: 1rem; text-align: center;">
                        <a href="liste_offres.php" class="btn btn-primary">Voir toutes les offres</a>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <p>Aucune offre disponible pour le moment.</p>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; 2025-2026 Uber-Cueillette - Tous droits réservés</p>
        <p>Institut Supérieur de Gestion de Tunis</p>
    </footer>
</body>
</html>