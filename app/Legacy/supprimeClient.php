<?php
    include("connexionbd.php");

    if (!empty($_GET["idClient"]))
     {
        $idClient = $_GET["idClient"];

        //check si client par default
        if ($idClient == 1) {
            $_SESSION["message"]["text"] = "Le client par défaut ne peut pas être supprimé.";
            $_SESSION["message"]["type"] = "danger";
            header("Location: ../vue/client.php");
            exit;
        }

        $sql = "UPDATE client SET etat=? WHERE id=?";
        $req = $connexion->prepare($sql);
        $req->execute([0,$idClient]);
    }


header("Location: ../vue/client.php");