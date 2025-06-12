<?php
    session_start();

    $nom_serveur = "localhost";
    $nom_base_de_donnes = "gestion_stock";
    $utilisateur = "root";
    $motpass = "";

    try {
        $connexion = new PDO("mysql:host=$nom_serveur;dbname=$nom_base_de_donnes", $utilisateur, $motpass);
        $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Check si client id 1 existe
        $stmt = $connexion->prepare("SELECT id FROM client WHERE id = 1");
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            // Insert client id 1 default client si il n'existe pas
            $insert = $connexion->prepare("INSERT INTO client (id, nom, prenom, adresse, telephone, etat) 
                                        VALUES (1, 'Client', 'Default', '/', '/', '1')");
            $insert->execute();
        }

        // Check si fournisseur id 1 existe
        $stmt = $connexion->prepare("SELECT id FROM fournisseur WHERE id = 1");
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            // Insert fournisseur id 1 default fournisseur si il n'existe pas
            $insert = $connexion->prepare("INSERT INTO fournisseur (id, nom, prenom, adresse, telephone, etat) 
                                        VALUES (1, 'Fournisseur', 'Default', '/', '/', '1')");
            $insert->execute();
        }

        return $connexion;
    } catch (Exception $e) {
        die("Erreur de connexion : ". $e->getMessage());
    }
?>