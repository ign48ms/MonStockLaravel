<?php
    include("connexionbd.php");


    if (!empty($_GET["idLigne"]) && !empty($_GET["idVente"])) {
        $idLigne = $_GET["idLigne"];
        $idVente = $_GET["idVente"];

        // get article info for quantite
        $sql = "SELECT id_article, quantite FROM vente_ligne WHERE id = ?";
        $req = $connexion->prepare($sql);
        $req->execute([$idLigne]);
        $ligne = $req->fetch(PDO::FETCH_ASSOC);
        if ($ligne) {
            // mise a jour quantite
            $sql = "UPDATE article SET quantite = quantite + ? WHERE id = ?";
            $req = $connexion->prepare($sql);
            $req->execute([$ligne["quantite"], $ligne["id_article"]]);

            // supprimer la ligne
            $sql = "DELETE FROM vente_ligne WHERE id = ?";
            $req = $connexion->prepare($sql);
            $req->execute([$idLigne]);

            // Recalcule de total
            $sql = "SELECT SUM(prix) AS total FROM vente_ligne WHERE id_vente = ?";
            $req = $connexion->prepare($sql);
            $req->execute([$idVente]);
            $result = $req->fetch(PDO::FETCH_ASSOC);
            $newTotal = $result['total'] ?? 0; // if there are no more lines, total = 0

            // mise a jour du vente
            $sql = "UPDATE vente SET total = ? WHERE id = ?";
            $req = $connexion->prepare($sql);
            $req->execute([$newTotal, $idVente]);
        }
    }
    header("Location: ../vue/vente.php?id_vente=" . $idVente);
    exit();
?>