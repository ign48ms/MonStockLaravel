<?php
  include_once("../model/function.php");
?>

<!DOCTYPE html>
<html lang="fr" dir="ltr">
  <head>
    <meta charset="UTF-8" />
    <title>
    <?php
      echo ucfirst(str_replace(".php","", basename($_SERVER["PHP_SELF"]))); //Pour afficher le nom de la page en haut
    ?>
    </title>
    <link rel="stylesheet" href="../public/css/styles.css" />
    <!-- Boxicons CDN Link -->
    <link
      href="https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css"
      rel="stylesheet"
    />

    <!-- Tom Select CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">



    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  </head>
  <body>
    <div class="sidebar hidden-print">
      <div class="logo-details">
      <i class='bx bx-cart-alt'></i>
        <span class="logo_name">MonStock</span>
      </div>
      <ul class="nav-links">
        <li>
          <a href="dashboard.php" class="<?php echo basename($_SERVER["PHP_SELF"])=="dashboard.php" ? "active" : "" ?>">
            <i class="bx bx-grid-alt"></i>
            <span class="links_name">Dashboard</span>
          </a>
        </li>
        <li>
          <a href="article.php" class="<?php echo basename($_SERVER["PHP_SELF"])=="article.php" ? "active" : "" ?>">
            <i class="bx bx-box"></i>
            <span class="links_name">Article</span>
          </a>
        </li>
        <li>
          <a href="vente.php" class="<?php echo basename($_SERVER["PHP_SELF"])=="vente.php" ? "active" : "" ?>">
          <i class="bx bx-shopping-bag"></i>
            <span class="links_name">Vente</span>
          </a>
        </li>
        <li>
          <a href="achat.php" class="<?php echo basename($_SERVER["PHP_SELF"])=="achat.php" ? "active" : "" ?>">
            <i class="bx bx-list-ul"></i>
            <span class="links_name">Achat</span>
          </a>
        </li>
        <li>
          <a href="client.php" class="<?php echo basename($_SERVER["PHP_SELF"])=="client.php" ? "active" : "" ?>">
            <i class="bx bx-user"></i>
            <span class="links_name">Client</span>
          </a>
        </li>
        <li>
          <a href="fournisseur.php" class="<?php echo basename($_SERVER["PHP_SELF"])=="fournisseur.php" ? "active" : "" ?>">
            <i class="bx bx-user"></i>
            <span class="links_name">Fournisseur</span>
          </a>
        </li>
        <li>
          <a href="categorie.php" class="<?php echo basename($_SERVER["PHP_SELF"])=="categorie.php" ? "active" : "" ?>">
          <i class='bx bx-category-alt'></i>
            <span class="links_name">Catégorie</span>
          </a>
        </li>
      </ul>
    </div>
    <section class="home-section">
      <nav class="hidden-print">
        <div class="sidebar-button">
          <i class="bx bx-menu sidebarBtn"></i>
          <span class="dashboard">
            <?php
            echo ucfirst(str_replace(".php","", basename($_SERVER["PHP_SELF"]))); 
            ?>
          </span>
        </div>
      </nav>