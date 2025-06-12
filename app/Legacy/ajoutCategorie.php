<?php
    include("connexionbd.php");
    if (!empty($_POST["libelle_categorie"])) {
        // Check if the category already exists
        $check_sql = "SELECT COUNT(*) FROM categorie_article WHERE libelle_categorie = ?";
        $check_req = $connexion->prepare($check_sql);
        $check_req->execute(array($_POST["libelle_categorie"]));
        $category_exists = (int)$check_req->fetchColumn();
        
        if ($category_exists > 0) {
            // Category already exists, return error message
            $_SESSION["message"]["text"] = "Cette catégorie existe déjà";
            $_SESSION["message"]["type"] = "warning";
        } else {
            // Category doesn't exist, proceed with insertion
            $sql = "INSERT INTO categorie_article(libelle_categorie) VALUES (?)";
            $req = $connexion->prepare($sql);
            $req->execute(array($_POST["libelle_categorie"]));
            
            if ($req->rowCount() != 0) {
                $_SESSION["message"]["text"] = "Catégorie ajoutée avec succès";
                $_SESSION["message"]["type"] = "success";
            } else {
                $_SESSION["message"]["text"] = "Une erreur s'est produite lors de l'ajout de la catégorie";
                $_SESSION["message"]["type"] = "danger";
            }
        }
    } else {
        $_SESSION["message"]["text"] = "Une information obligatoire non renseignée";
        $_SESSION["message"]["type"] = "danger";
    }
    header("Location: ../vue/categorie.php");
?>