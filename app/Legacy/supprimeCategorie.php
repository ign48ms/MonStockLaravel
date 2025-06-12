<?php
    include("connexionbd.php");

    if (!empty($_GET["idCategorie"]))
     {
        $idCategorie = $_GET["idCategorie"];
        $sql = "UPDATE categorie_article SET etat=? WHERE id=?";
        $req = $connexion->prepare($sql);
        $req->execute([0,$idCategorie]);
    }


header("Location: ../vue/categorie.php");