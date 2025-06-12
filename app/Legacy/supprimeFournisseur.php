<?php
    include("connexionbd.php");

    if (!empty($_GET["idFournisseur"]))
     {
        $idFournisseur = $_GET["idFournisseur"];

        //check si fournissseur par default
        if ($idFournisseur == 1) {
            $_SESSION["message"]["text"] = "Le fournisseur par défaut ne peut pas être supprimé.";
            $_SESSION["message"]["type"] = "danger";
            header("Location: ../vue/fournisseur.php");
            exit;
        }

        $sql = "UPDATE fournisseur SET etat=? WHERE id=?";
        $req = $connexion->prepare($sql);
        $req->execute([0,$idFournisseur]);
    }


header("Location: ../vue/fournisseur.php");