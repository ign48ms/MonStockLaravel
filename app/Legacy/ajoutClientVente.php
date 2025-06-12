<?php
include("connexionbd.php");

if (!empty($_POST["id_client"]) && !empty($_POST["id_vente"])) {
    $id_client = $_POST["id_client"];
    $id_vente = $_POST["id_vente"];

    // Assign the client to the vente
    $sql = "UPDATE vente SET id_client = ?, etat = '1' WHERE id = ?";
    $req = $connexion->prepare($sql);
    $req->execute([$id_client, $id_vente]);

    if ($req->rowCount() > 0) {
        $_SESSION["message"]["type"] = "success";
    } else {
        $_SESSION["message"]["text"] = "Erreur lors de la validation de la vente.";
        $_SESSION["message"]["type"] = "danger";
    }
}

header("Location: ../vue/vente.php");
exit;
?>
