<?php
include("connexionbd.php");

// Create a new sale with default client assigned
$sql = "INSERT INTO achat (id_fournisseur, total, date_achat, etat) VALUES (1, 0, NOW(), 1)";
$req = $connexion->prepare($sql);
$req->execute();

if ($req->rowCount() > 0) {
    $id_achat = $connexion->lastInsertId();
    header("Location: ../vue/achat.php?id_achat=" . $id_achat);
    exit;
} else {
    $_SESSION["message"]["text"] = "Erreur lors de la création de la achat.";
    $_SESSION["message"]["type"] = "danger";
    header("Location: ../vue/achat.php");
    exit;
}
?>