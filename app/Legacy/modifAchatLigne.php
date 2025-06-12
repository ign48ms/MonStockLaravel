<?php
    include("connexionbd.php");
    
    if (!empty($_POST["id_ligne"]) && 
        !empty($_POST["id_article"]) && 
        !empty($_POST["quantite"]) && 
        !empty($_POST["prix"]) && 
        !empty($_POST["id_achat"])) {
        
        // Get the current achat_ligne data to calculate stock adjustments
        $sql_get_current = "SELECT quantite, id_article FROM achat_ligne WHERE id = ?";
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
                // Remove the old quantity from old article's stock
                $sql_restore = "UPDATE article SET quantite = quantite - ? WHERE id = ?";
                $req_restore = $connexion->prepare($sql_restore);
                $req_restore->execute([$old_quantity, $old_article_id]);
                
                // Add new quantity to new article's stock
                $sql_add = "UPDATE article SET quantite = quantite + ? WHERE id = ?";
                $req_add = $connexion->prepare($sql_add);
                $req_add->execute([$new_quantity, $new_article_id]);
            } 
            // If only quantity changed but article is the same
            else if ($old_quantity != $new_quantity) {
                $quantity_diff = $new_quantity - $old_quantity;
                
                // Adjust stock based on quantity difference
                $sql_adjust = "UPDATE article SET quantite = quantite + ? WHERE id = ?";
                $req_adjust = $connexion->prepare($sql_adjust);
                $req_adjust->execute([$quantity_diff, $new_article_id]);
            }
            
            // Update the achat_ligne record
            $sql_update_ligne = "UPDATE achat_ligne SET id_article = ?, quantite = ?, prix = ? WHERE id = ?";
            $req_update_ligne = $connexion->prepare($sql_update_ligne);
            $req_update_ligne->execute([
                $new_article_id,
                $new_quantity,
                $_POST["prix"],
                $_POST["id_ligne"]
            ]);
            
            // Update total in achat table
            $sql_update_total = "UPDATE achat SET total = (
                SELECT SUM(prix) FROM achat_ligne WHERE id_achat = ?
            ) WHERE id = ?";
            $req_update_total = $connexion->prepare($sql_update_total);
            $req_update_total->execute([$_POST["id_achat"], $_POST["id_achat"]]);
            
            // Commit all changes if successful
            $connexion->commit();
            
            $_SESSION["message"]["text"] = "Ligne d'achat modifiée avec succès";
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
    
    // Redirect back to achat page
    header("Location: ../vue/achat.php?id_achat=" . $_POST["id_achat"]);
?>