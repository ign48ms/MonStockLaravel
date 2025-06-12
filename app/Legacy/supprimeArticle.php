<?php
    include("connexionbd.php");

    if (!empty($_GET["idArticle"]))
     {
        $idArticle = $_GET["idArticle"];
        $sql = "UPDATE article SET etat=? WHERE id=?";
        $req = $connexion->prepare($sql);
        $req->execute([0,$idArticle]);
    }


header("Location: ../vue/article.php");