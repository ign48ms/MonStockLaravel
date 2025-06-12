<?php
    include("connexionbd.php");
    
    if (!empty($_POST["id_ligne"]) && 
        !empty($_POST["id_article"]) && 
        !empty($_POST["quantite"]) && 
        !empty($_POST["prix"]) && 
        !empty($_POST["id_vente"])) {
        
        // Get the current vente_ligne data to calculate stock adjustments
        $sql_get_current = "SELECT quantite, id_article FROM vente_ligne WHERE id = ?";
        $req_get_current = $connexion->prepare($sql_get_current);
        $req_get_current->execute([$_POST["id_ligne"]]);
        $current_data = $req_get_current->fetch(PDO::FETCH_ASSOC);
        
        // Start transaction for data consistency
        $connexion->beginTransaction();
        
        try {
            // Handle stock adjustments
            $old_article_id = $current_data["id_article"];
            $old_quantity = $current_data["quantite"];
            $new_article_id = $_POST["id_article"];
            $new_quantity = $_POST["quantite"];
            
            // If article changed
            if ($old_article_id != $new_article_id) {
                // Return old quantity to old article's stock
                $sql_restore = "UPDATE article SET quantite = quantite + ? WHERE id = ?";
                $req_restore = $connexion->prepare($sql_restore);
                $req_restore->execute([$old_quantity, $old_article_id]);
                
                // Remove new quantity from new article's stock
                $sql_subtract = "UPDATE article SET quantite = quantite - ? WHERE id = ?";
                $req_subtract = $connexion->prepare($sql_subtract);
                $req_subtract->execute([$new_quantity, $new_article_id]);
            } 
            // If only quantity changed but article is the same
            else if ($old_quantity != $new_quantity) {
                $quantity_diff = $new_quantity - $old_quantity;
                
                // Adjust stock based on quantity difference
                $sql_adjust = "UPDATE article SET quantite = quantite - ? WHERE id = ?";
                $req_adjust = $connexion->prepare($sql_adjust);
                $req_adjust->execute([$quantity_diff, $new_article_id]);
            }
            
            // Update the vente_ligne record
            $sql_update_ligne = "UPDATE vente_ligne SET id_article = ?, quantite = ?, prix = ? WHERE id = ?";
            $req_update_ligne = $connexion->prepare($sql_update_ligne);
            $req_update_ligne->execute([
                $new_article_id,
                $new_quantity,
                $_POST["prix"],
                $_POST["id_ligne"]
            ]);
            
            // Update total in vente table
            $sql_update_total = "UPDATE vente SET total = (
                SELECT SUM(prix) FROM vente_ligne WHERE id_vente = ?
            ) WHERE id = ?";
            $req_update_total = $connexion->prepare($sql_update_total);
            $req_update_total->execute([$_POST["id_vente"], $_POST["id_vente"]]);
            
            // Commit all changes if successful
            $connexion->commit();
            
            $_SESSION["message"]["text"] = "Ligne de vente modifiée avec succès";
            $_SESSION["message"]["type"] = "success";
        } 
        catch (Exception $e) {
            // Rollback on error
            $connexion->rollback();
            $_SESSION["message"]["text"] = "Erreur lors de la modification: " . $e->getMessage();
            $_SESSION["message"]["type"] = "danger";
        }
    } 
    else {
        $_SESSION["message"]["text"] = "Une information obligatoire non renseignée";
        $_SESSION["message"]["type"] = "danger";
    }
    
    // Redirect back to vente page
    header("Location: ../vue/vente.php?id_vente=" . $_POST["id_vente"]);
?>