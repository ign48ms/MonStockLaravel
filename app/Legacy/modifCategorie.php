<?php
    include("connexionbd.php");
    if (!empty($_POST["libelle_categorie"]) && !empty($_POST["id"])) {
        // Check if another category with the same name already exists (excluding current category)
        $check_sql = "SELECT COUNT(*) FROM categorie_article WHERE libelle_categorie = ? AND id != ?";
        $check_req = $connexion->prepare($check_sql);
        $check_req->execute(array($_POST["libelle_categorie"], $_POST["id"]));
        $category_exists = (int)$check_req->fetchColumn();
        
        if ($category_exists > 0) {
            // Category already exists, return error message
            $_SESSION["message"]["text"] = "Cette catégorie existe déjà";
            $_SESSION["message"]["type"] = "warning";
        } else {
            // Proceed with update
            $sql = "UPDATE categorie_article SET libelle_categorie=? WHERE id=?";
            $req = $connexion->prepare($sql);
            $req->execute(array(
                $_POST["libelle_categorie"],
                $_POST["id"]
            ));
            
            if ($req->rowCount() != 0) {
                $_SESSION["message"]["text"] = "Catégorie modifiée avec succès";
                $_SESSION["message"]["type"] = "success";
            } else {
                $_SESSION["message"]["text"] = "Rien n'a été modifié";
                $_SESSION["message"]["type"] = "warning";
            }
        }
    } else {
        $_SESSION["message"]["text"] = "Une information obligatoire non renseignée";
        $_SESSION["message"]["type"] = "danger";
    }
    header("Location: ../vue/categorie.php");
?>