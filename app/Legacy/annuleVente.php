<?php
include("connexionbd.php");

if (!empty($_GET["idVente"])) {
    $idVente = $_GET["idVente"];

    // Set etat=0
    $sql = "UPDATE vente SET etat = '0' WHERE id = ?";
    $req = $connexion->prepare($sql);
    $req->execute([$idVente]);

    if ($req->rowCount() != 0) {
        // Get all articles from vente_ligne for this vente
        $sql = "SELECT id_article, quantite FROM vente_ligne WHERE id_vente = ?";
        $req = $connexion->prepare($sql);
        $req->execute([$idVente]);
        $articles = $req->fetchAll(PDO::FETCH_ASSOC);

        // Update quantite
        foreach ($articles as $article) {
            $sql = "UPDATE article SET quantite = quantite + ? WHERE id = ?";
            $req = $connexion->prepare($sql);
            $req->execute([$article["quantite"], $article["id_article"]]);
        }
    }
}

header("Location: ../vue/vente.php");
exit();
