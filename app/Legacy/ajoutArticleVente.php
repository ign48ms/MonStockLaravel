<?php
include("connexionbd.php");

if (!empty($_POST["id_vente"]) && !empty($_POST["id_article"]) && !empty($_POST["quantite"])) {
    $id_vente = $_POST["id_vente"];
    $id_article = $_POST["id_article"];
    $quantite = $_POST["quantite"];
    $prix_unitaire_custom = $_POST["prix_u"];

    // quantite disponible de l'article
    $sql = "SELECT quantite FROM article WHERE id = ?";
    $req = $connexion->prepare($sql);
    $req->execute([$id_article]);
    $article = $req->fetch();

    if (!$article) {
        $_SESSION["message"]["text"] = "Article non trouvé.";
        $_SESSION["message"]["type"] = "danger";
        header("Location: ../vue/vente.php?id_vente=" . $id_vente);
        exit;
    }

    $stock_disponible = (int)$article["quantite"];
    

    // Check if there is enough stock
    if ($quantite > $stock_disponible) {
        //store form values
        $_SESSION["form_values"] = [
            "id_article" => $id_article,
            "quantite" => $quantite,
            "prix_u" => $prix_unitaire_custom,
            "prix" => $prix_unitaire_custom * $quantite
        ];

        $_SESSION["message"]["text"] = "Quantité insuffisante en stock ! Disponible: $stock_disponible";
        $_SESSION["message"]["type"] = "danger";
        header("Location: ../vue/vente.php?id_vente=" . $id_vente);
        exit;
    }

    $prix_total = $prix_unitaire_custom * $quantite;

    // insert into vente_ligne
    $sql = "INSERT INTO vente_ligne (id_vente, id_article, quantite, prix) VALUES (?, ?, ?, ?)";
    $req = $connexion->prepare($sql);
    $req->execute([$id_vente, $id_article, $quantite, $prix_total]);

    // misajour total
    $sql = "UPDATE vente SET total = total + ? WHERE id = ?";
    $req = $connexion->prepare($sql);
    $req->execute([$prix_total, $id_vente]);

    //update quantite
    $sql = "UPDATE article SET quantite = quantite - ? WHERE id = ?";
    $req = $connexion->prepare($sql);
    $req->execute([$quantite, $id_article]);

    $_SESSION["message"]["text"] = "Article ajouté avec succès.";
    $_SESSION["message"]["type"] = "success";
} else {
    $_SESSION["message"]["text"] = "Veuillez remplir tous les champs.";
    $_SESSION["message"]["type"] = "danger";
}

header("Location: ../vue/vente.php?id_vente=" . $id_vente);
exit;
?>
