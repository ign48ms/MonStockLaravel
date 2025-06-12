<?php
    include("entete.php");
?>
<div class="home-content">
        <div class="overview-boxes">
          <div class="box">
            <div class="right-side">
              <div class="box-topic">Achats</div>
              <div class="number"> <?php echo getAllAchat()["nbre"] ?> </div>
             
            </div>
            <i class="bx bx-cart-alt cart"></i>
          </div>
          <div class="box">
            <div class="right-side">
              <div class="box-topic">Ventes</div>
              <div class="number"><?php echo getAllVente()["nbre"] ?></div>
             
            </div>
            <i class="bx bxs-cart-add cart two"></i>
          </div>
          <div class="box <?= LowStock() ? 'alert-low-stock tooltip' : '' ?>" <?= LowStock() ? 'data-tooltip="Stock Faible"' : '' ?>>
            <div class="right-side">
              <div class="box-topic">Articles</div>
              <div class="number"><?php echo getAllArticle()["nbre"] ?></div>
             
            </div>
            <i class="bx bx-cart cart three"></i>
          </div>
          <div class="box">
            <div class="right-side">
              <div class="box-topic">Chiffre d'affaire</div>
              <div class="number"><?php echo (getCA()["total"] !== null ? number_format(getCA()["total"]) : "0") . " DZD" ?></div>
             
            </div>
            <i class="bx bxs-cart-download cart four"></i>
          </div>
        </div>
        <div class="sales-boxes">
          <div class="recent-sales vabox">
            <div class="title">Ventes recentes</div>
            <?php
              $ventes = getLastVente();
              if (!empty($ventes)) {
            ?>
            <div class="sales-details">
              <ul class="details">
                <li class="topic">Date</li>
                <?php
                  foreach ($ventes as $key => $value) {
                    ?>
                      <li><?php echo date("d M Y",strtotime($value["date_vente"])) ?></li>
                    <?php
                  }
                ?>
              </ul>
              <ul class="details">
                <li class="topic">Client</li>
                <?php
                  foreach ($ventes as $key => $value) {
                    ?>
                      <li><a href="client.php"><?php echo $value["nom"]." ". $value["prenom"] ?></a></li>
                    <?php
                  }
                ?>
              </ul>
              <ul class="details">
                <li class="topic">Total</li>
                <?php
                  foreach ($ventes as $key => $value) {
                    ?>
                      <li><?php echo number_format($value["total"])." DZD"?></li>
                    <?php
                  }
                ?>
              </ul>
            </div>
            <?php
              } else {
            ?>
              <p style="text-align: center; margin-top: 20px;">Aucune vente récente</p>
            <?php
              }
            ?>
            <div class="button">
              <a href="vente.php">Voir Tout</a>
            </div>
          </div>
          <div class="top-sales box">
            <div class="title">Articles les plus vendus</div>
            <ul class="top-sales-details">
            <?php
              $article = getMostVente();
              if (!empty($article)) {
                foreach ($article as $key => $value) {
                ?>
                <li>
                  <a href="article.php">
                    <span class="product"><?php echo $value["nom_article"] ?></span>
                  </a>
                  <span class="price"><?php echo number_format($value["prix"])." DZD"?></span>
                </li>
                <?php
                }
              } else {
            ?>
              <li style="text-align: center; margin-top: 20px;">Aucun article vendu</li>
            <?php
              }
            ?>
            </ul>
          </div>
        </div>
      </div>
    </section>
<?php
    include("pied.php");
?>