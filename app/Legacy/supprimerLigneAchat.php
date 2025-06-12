<?php
    include("connexionbd.php");


    if (!empty($_GET["idLigne"]) && !empty($_GET["idAchat"])) {
        $idLigne = $_GET["idLigne"];
        $idAchat = $_GET["idAchat"];

        // get article info for quantite
        $sql = "SELECT id_article, quantite FROM achat_ligne WHERE id = ?";
        $req = $connexion->prepare($sql);
        $req->execute([$idLigne]);
        $ligne = $req->fetch(PDO::FETCH_ASSOC);
        if ($ligne) {
            // mise a jour quantite
            $sql = "UPDATE article SET quantite = quantite - ? WHERE id = ?";
            $req = $connexion->prepare($sql);
            $req->execute([$ligne["quantite"], $ligne["id_article"]]);

            // supprimer la ligne
            $sql = "DELETE FROM achat_ligne WHERE id = ?";
            $req = $connexion->prepare($sql);
            $req->execute([$idLigne]);

            // Recalcule de total
            $sql = "SELECT SUM(prix) AS total FROM achat_ligne WHERE id_achat = ?";
            $req = $connexion->prepare($sql);
            $req->execute([$idAchat]);
            $result = $req->fetch(PDO::FETCH_ASSOC);
            $newTotal = $result['total'] ?? 0; // if there are no more lines, total = 0

            // mise a jour du vente
            $sql = "UPDATE achat SET total = ? WHERE id = ?";
            $req = $connexion->prepare($sql);
            $req->execute([$newTotal, $idAchat]);
        }
    }
    header("Location: ../vue/achat.php?id_achat=" . $idAchat);
    exit();
?>