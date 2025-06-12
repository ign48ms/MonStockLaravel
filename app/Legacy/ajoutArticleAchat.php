<?php
session_start();
include("connexionbd.php");

if (!empty($_POST["id_achat"]) && !empty($_POST["id_article"]) && !empty($_POST["quantite"])) {
    $id_achat = $_POST["id_achat"];
    $id_article = $_POST["id_article"];
    $quantite = $_POST["quantite"];
    $prix_u = $_POST["prix_u"];
    

    // prix article
    $sql = "SELECT prix_achat_unitaire, quantite FROM article WHERE id = ?";
    $req = $connexion->prepare($sql);
    $req->execute([$id_article]);
    $article = $req->fetch();

    if (!$article) {
        $_SESSION["message"]["text"] = "Article non trouvé.";
        $_SESSION["message"]["type"] = "danger";
        header("Location: ../vue/achat.php?id_achat=" . $id_achat);
        exit;
    }

    $total_price = $prix_u * $quantite;

    // insert into achat_ligne
    $sql = "INSERT INTO achat_ligne (id_achat, id_article, quantite, prix) VALUES (?, ?, ?, ?)";
    $req = $connexion->prepare($sql);
    $req->execute([$id_achat, $id_article, $quantite, $total_price]);

    // mise a jour total
    $sql = "UPDATE achat SET total = total + ? WHERE id = ?";
    $req = $connexion->prepare($sql);
    $req->execute([$total_price, $id_achat]);

    // PPM - Calcul du nouveau prix moyen pondéré
    $ancienne_quantite = $article["quantite"];
    $ancien_prix = $article["prix_achat_unitaire"];

    $nouvelle_quantite = $ancienne_quantite + $quantite;

    if ($nouvelle_quantite > 0) {
        $nouveau_prix_moyen = (($ancienne_quantite * $ancien_prix) + ($quantite * $prix_u)) / $nouvelle_quantite;
    } else {
        $nouveau_prix_moyen = $prix_u; // fallback en cas d'erreur
    }

    // mise a jour article (nouvelle quantité et nouveau prix moyen)
    $sql = "UPDATE article SET quantite = ?, prix_achat_unitaire = ? WHERE id = ?";
    $req = $connexion->prepare($sql);
    $req->execute([$nouvelle_quantite, $nouveau_prix_moyen, $id_article]);


    $_SESSION["message"]["text"] = "Article ajouté avec succès.";
    $_SESSION["message"]["type"] = "success";
} else {
    $_SESSION["message"]["text"] = "Veuillez remplir tous les champs.";
    $_SESSION["message"]["type"] = "danger";
}

header("Location: ../vue/achat.php?id_achat=" . $id_achat);
exit;
?>
