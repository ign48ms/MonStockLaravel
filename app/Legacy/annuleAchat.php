<?php
include("connexionbd.php");

if (!empty($_GET["idAchat"])) {
    $idAchat = $_GET["idAchat"];

    // Set etat=0
    $sql = "UPDATE achat SET etat = '0' WHERE id = ?";
    $req = $connexion->prepare($sql);
    $req->execute([$idAchat]);

    if ($req->rowCount() != 0) {
        // Get all articles from achat_ligne for this achat
        $sql = "SELECT id_article, quantite FROM achat_ligne WHERE id_achat = ?";
        $req = $connexion->prepare($sql);
        $req->execute([$idAchat]);
        $articles = $req->fetchAll(PDO::FETCH_ASSOC);

        // Update quantite
        foreach ($articles as $article) {
            $sql = "UPDATE article SET quantite = quantite - ? WHERE id = ?";
            $req = $connexion->prepare($sql);
            $req->execute([$article["quantite"], $article["id_article"]]);
        }
    }
}

header("Location: ../vue/achat.php");
exit();
