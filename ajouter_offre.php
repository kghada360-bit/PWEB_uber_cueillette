<?php
require_once 'config.php';

if(!estAgriculteur()) {
    redirect('login.php');
}

$agriculteur_id = $_SESSION['user_id'];
$erreur = '';
$succes = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $type_fruit = nettoyer($_POST['type_fruit']);
    $gouvernorat = nettoyer($_POST['gouvernorat']);
    $adresse_recolte = nettoyer($_POST['adresse_recolte']);
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'];
    $nb_ouvriers_demandes = intval($_POST['nb_ouvriers_demandes']);
    $prix_journee = floatval($_POST['prix_journee']);
    $date_limite_postulation = $_POST['date_limite_postulation'];
    
    $erreurs = [];
    
    if($date_debut < date('Y-m-d')) {
        $erreurs[] = "La date de début ne peut pas être dans le passé";
    }
    if($date_fin <= $date_debut) {
        $erreurs[] = "La date de fin doit être après la date de début";
    }
    if($date_limite_postulation >= $date_debut) {
        $erreurs[] = "La date limite de postulation doit être avant la date de début";
    }
    if($nb_ouvriers_demandes < 1 || $nb_ouvriers_demandes > 100) {
        $erreurs[] = "Le nombre d'ouvriers doit être entre 1 et 100";
    }
    if($prix_journee < 10 || $prix_journee > 200) {
        $erreurs[] = "Le prix à la journée doit être entre 10 et 200 DT";
    }
    
    if(empty($erreurs)) {
        try {
            $sql = "INSERT INTO offres (agriculteur_id, type_fruit, gouvernorat, adresse_recolte, 
                    date_debut, date_fin, nb_ouvriers_demandes, prix_journee, date_limite_postulation) 
                    VALUES (:agriculteur_id, :type_fruit, :gouvernorat, :adresse_recolte, 
                    :date_debut, :date_fin, :nb_ouvriers_demandes, :prix_journee, :date_limite_postulation)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':agriculteur_id' => $agriculteur_id,
                ':type_fruit' => $type_fruit,
                ':gouvernorat' => $gouvernorat,
                ':adresse_recolte' => $adresse_recolte,
                ':date_debut' => $date_debut,
                ':date_fin' => $date_fin,
                ':nb_ouvriers_demandes' => $nb_ouvriers_demandes,
                ':prix_journee' => $prix_journee,
                ':date_limite_postulation' => $date_limite_postulation
            ]);
            $succes = "Offre ajoutée avec succès !";
        } catch(PDOException $e) {
            $erreur = "Erreur lors de l'ajout : " . $e->getMessage();
        }
    } else {
        $erreur = implode('<br>', $erreurs);
    }
}

$types_fruits = ['Olives', 'Agrumes', 'Tomates', 'Pommes', 'Poires', 'Pêches', 'Abricots', 'Raisins', 'Fraises', 'Figues'];
$gouvernorats = ['Tunis', 'Ariana', 'Ben Arous', 'Manouba', 'Nabeul', 'Zaghouan', 'Bizerte', 'Béja', 'Jendouba', 'Le Kef', 'Siliana', 'Sousse', 'Monastir', 'Mahdia', 'Sfax', 'Kairouan', 'Kasserine', 'Sidi Bouzid', 'Gabès', 'Médenine', 'Tataouine', 'Gafsa', 'Tozeur', 'Kébili'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une offre - Uber-Cueillette</title>
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
            <h2>➕ Ajouter une offre de récolte</h2>
            
            <?php if($erreur): ?>
                <div class="alert alert-error"><?= $erreur ?></div>
            <?php endif; ?>
            
            <?php if($succes): ?>
                <div class="alert alert-success"><?= $succes ?></div>
                <p><a href="mes_offres.php" class="btn btn-primary">Voir mes offres</a></p>
            <?php else: ?>
                <form method="POST" action="" onsubmit="return validerOffre()">
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label>Type de fruit *</label>
                                <select name="type_fruit" id="type_fruit" required>
                                    <option value="">Sélectionnez</option>
                                    <?php foreach($types_fruits as $fruit): ?>
                                        <option value="<?= $fruit ?>"><?= $fruit ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label>Gouvernorat *</label>
                                <select name="gouvernorat" id="gouvernorat" required>
                                    <option value="">Sélectionnez</option>
                                    <?php foreach($gouvernorats as $gouv): ?>
                                        <option value="<?= $gouv ?>"><?= $gouv ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Adresse du site de récolte *</label>
                        <input type="text" name="adresse_recolte" id="adresse_recolte" required>
                    </div>
                    
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label>Date de début *</label>
                                <input type="date" name="date_debut" id="date_debut" required>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label>Date de fin *</label>
                                <input type="date" name="date_fin" id="date_fin" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label>Nombre d'ouvriers demandés *</label>
                                <input type="number" name="nb_ouvriers_demandes" id="nb_ouvriers_demandes" min="1" max="100" required>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label>Prix à la journée (DT) *</label>
                                <input type="number" name="prix_journee" id="prix_journee" step="0.5" min="10" max="200" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Date limite de postulation *</label>
                        <input type="date" name="date_limite_postulation" id="date_limite_postulation" required>
                        <small>Les candidatures seront closes après cette date</small>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Publier l'offre</button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <p>&copy; 2025-2026 Uber-Cueillette</p>
    </footer>
</body>
</html>