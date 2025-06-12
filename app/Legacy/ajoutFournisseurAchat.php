<?php
include("connexionbd.php");

if (!empty($_POST["id_fournisseur"]) && !empty($_POST["id_achat"])) {
    $id_fournisseur = $_POST["id_fournisseur"];
    $id_achat = $_POST["id_achat"];

    // Assign le fournisseur to the vente
    $sql = "UPDATE achat SET id_fournisseur = ?, etat = '1' WHERE id = ?";
    $req = $connexion->prepare($sql);
    $req->execute([$id_fournisseur, $id_achat]);

    if ($req->rowCount() > 0) {
        $_SESSION["message"]["text"] = "Achat validée avec succès.";
        $_SESSION["message"]["type"] = "success";
    } else {
        $_SESSION["message"]["text"] = "Erreur lors de la validation de l'achat.";
        $_SESSION["message"]["type"] = "danger";
    }
}

header("Location: ../vue/achat.php?");
exit;
?>
