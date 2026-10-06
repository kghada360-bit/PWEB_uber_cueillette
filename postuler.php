<?php
require_once 'config.php';

if(!estOuvrier()) {
    redirect('login.php');
}

$offre_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$ouvrier_id = $_SESSION['user_id'];

$sql_check = "SELECT * FROM candidatures WHERE offre_id = :offre_id AND ouvrier_id = :ouvrier_id";
$stmt = $conn->prepare($sql_check);
$stmt->execute([':offre_id' => $offre_id, ':ouvrier_id' => $ouvrier_id]);
$existe = $stmt->fetch();

if($existe) {
    $_SESSION['erreur'] = "Vous avez déjà postulé à cette offre";
    redirect('liste_offres.php');
}

$sql_offre = "SELECT * FROM offres WHERE id = :id AND statut = 'ouverte' 
              AND date_limite_postulation >= CURDATE() 
              AND nb_ouvriers_acceptes < nb_ouvriers_demandes";
$stmt = $conn->prepare($sql_offre);
$stmt->execute([':id' => $offre_id]);
$offre = $stmt->fetch();

if($offre) {
    $sql = "INSERT INTO candidatures (offre_id, ouvrier_id, statut) VALUES (:offre_id, :ouvrier_id, 'en_attente')";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':offre_id' => $offre_id, ':ouvrier_id' => $ouvrier_id]);
    $_SESSION['succes'] = "Candidature envoyée avec succès !";
} else {
    $_SESSION['erreur'] = "Cette offre n'est plus disponible";
}

redirect('liste_offres.php');
?>