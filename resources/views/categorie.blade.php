<?php 
    include("entete.php");

    if (!empty($_GET["id"])) {
        $categorie = getCategorie($_GET["id"]);
    }
?>

<div class="home-content">
    <div class="overview-boxes">
        <div class="vabox">
            <form action=" <?= !empty($_GET["id"]) ? "../model/modifCategorie.php" : "../model/ajoutCategorie.php" ?>" method="post">
                <h3><?= !empty($_GET["id"]) ? "Modifier Catégorie" : "Ajouter une Catégorie" ?></h3>
                <label for="libelle_categorie">Libellé</label>
                <input value="<?= !empty($_GET["id"]) ? $categorie["libelle_categorie"] : "" ?>" type="text" name="libelle_categorie" id="libelle_categorie" placeholder="Veuillez saisir le nom">
                <input value="<?= !empty($_GET["id"]) ? $categorie["id"] : "" ?>" type="hidden" name="id" id="id">

                <button type="submit">Ajouter</button>
                
                <?php
                if (!empty($_SESSION["message"]["text"])) {
                ?>
                    <div class="alert <?=$_SESSION["message"]["type"]?>">
                        <?=$_SESSION["message"]["text"]?>
                    </div>
                <?php 
                unset($_SESSION["message"]); // Efface le message après affichage
                }
                ?>

            </form>
        </div>
        <div class="vabox">
            <table class="mtable">
                <tr>
                    <th>Libellé</th>
                    <th>Action</th>
                </tr>
                <?php
                $categories = getCategorie();
                if (!empty($categories) && is_array( $categories )) {
                    foreach ($categories as $key => $value) {
                    ?>
                    <tr>
                        <td><?=$value["libelle_categorie"]?></td>
                        <td>
                            <a href="?id=<?=$value["id"]?>" title="Modifier" style="color: blue !important;"><i class='bx bx-edit-alt'></i></a>
                            <a onclick="supprimeCategorie(<?= $value['id']?>)" title="Supprimer" style="color: red; cursor: pointer;"><i class='bx bx-x-circle'></i></a>
                        </td>
                    </tr>    
                <?php
                    }
                }
                ?>
            </table>
        </div>
    </div>
</div>

</section>

<?php 
    include("pied.php");
?>

<script>
    function supprimeCategorie(idCategorie) {

    if (confirm("Voulez-vous vraiment supprimer ce categorie?")) {
        window.location.href = `../model/supprimeCategorie.php?idCategorie=${idCategorie}`;
    }
}
</script>