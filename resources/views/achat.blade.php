@extends('layouts.app')

@section('title', 'Achat - MonStock')
@section('page-title', 'Achat')

@section('content')


    <?php if (empty($_GET["id_achat"])): ?>
        <button onclick="createNewAchat()" class="valider" style="margin-left: 25px; margin-bottom: 5px;">Nouveau Achat</button>
    <?php endif; ?>  
    <div class="overview-boxes">
        <div class="va-container">

        <?php if (!empty($_GET["id_achat"])): ?>
        <div class="vabox">
            <form action="../model/ajoutFournisseurAchat.php" method="post" id="achat-form" <?php echo (!empty($_GET["edit_ligne"])) ? 'style="display: none;"' : ''; ?>>
                <input type="hidden" name="id_achat" value="<?= $_GET['id_achat'] ?>">
                <label for="id_fournisseur">Fournisseur</label>
                    <select name="id_fournisseur" id="id_fournisseur" class="tom-select">
                    <?php 
                        $fournisseurs = getFournisseur();
                        if (!empty($fournisseurs) && is_array($fournisseurs)) {
                            foreach ($fournisseurs as $key => $value) {
                                $selected = (!empty($achat) && $achat["id_fournisseur"] == $value["id"]) ? "selected" : "";
                                echo "<option value='{$value["id"]}' {$selected}>{$value["nom"]} {$value["prenom"]}</option>";
                            }
                        }
                    ?>  
                    </select>
                    <button class="valider" type="submit">Valider Achat</button>
            </form>
            <div class="vabox">
                <form action="<?php echo (!empty($_GET["edit_ligne"])) ? '../model/modifAchatLigne.php' : '../model/ajoutArticleAchat.php'; ?>" method="post" id="article-form">
                    <input type="hidden" name="id_achat" value="<?= $_GET['id_achat'] ?>">

                    <?php if (!empty($_GET["edit_ligne"])): 
                        // Fetch the ligne data for editing
                        $lignes = getAchatLignes($_GET["id_achat"]);
                        $edit_ligne = null;
                        foreach ($lignes as $ligne) {
                            if ($ligne["id"] == $_GET["edit_ligne"]) {
                                $edit_ligne = $ligne;
                                break;
                            }
                        }
                        if ($edit_ligne): 
                    ?>
                    <input type="hidden" name="id_ligne" value="<?= $edit_ligne["id"] ?>">
                    <?php endif; endif; ?>

                    <label for="id_article">Article</label>
                    <select onchange="remplirPrix()" name="id_article" id="id_article" class="tom-select">
                        <option value="" disabled <?php echo (empty($_GET["edit_ligne"]) || empty($edit_ligne)) ? 'selected' : ''; ?>>Choisir un article</option>
                        <?php 
                            $articles = getArticle();
                            if (!empty($articles) && is_array($articles)) {
                                foreach ($articles as $key => $value) {
                                    // Check if we're in edit mode and this is the selected article
                                    $selected = (!empty($edit_ligne) && $edit_ligne["id_article"] == $value["id"]) ? 'selected' : '';
                                    echo "<option data-prix='{$value["prix_achat_unitaire"]}' value='{$value["id"]}' {$selected}>{$value["nom_article"]} - {$value["quantite"]} disponible</option>";                                   
                                }
                            }
                        ?> 
                    </select>

                    <label for="quantite">Quantité</label>
                    <input onkeyup="setPrix()" type="number" name="quantite" id="quantite" placeholder="Veuillez saisir la quantité" min="1" value="<?php echo (!empty($edit_ligne)) ? $edit_ligne["quantite"] : ''; ?>">

                    <label for="prix_u">Prix unitaire</label>
                    <input onkeyup="setPrix()" type="number" name="prix_u" id="prix_u" placeholder="Veuillez saisir le prix unitaire" min="0" step="any" value="<?php echo (!empty($edit_ligne)) ? ($edit_ligne["prix"] / $edit_ligne["quantite"]) : ''; ?>">

                    <label for="prix">Prix total</label>
                    <input type="number" name="prix" id="prix" placeholder="Prix total" min="0" step="any" value="<?php echo (!empty($edit_ligne)) ? $edit_ligne["prix"] : ''; ?>">

                    <button type="submit"><?php echo (!empty($_GET["edit_ligne"])) ? 'Modifier' : 'Ajouter'; ?></button>
                    <?php if (!empty($_GET["edit_ligne"])): ?>
                        <button type="button" onclick="window.location.href='achat.php?id_achat=<?= $_GET['id_achat'] ?>'" class="valider">Annuler</button>
                    <?php endif; ?>

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
        </div>
        <div style="display: block;" class="box">
                <h3>Détails de l'achat</h3>
                <table class="mtable">
                    <tr>
                        <th class="sortable" data-sort="article">Article <span class="sort-icon"></span></th>
                        <th class="sortable" data-sort="montant">Quantité <span class="sort-icon"></span></th>
                        <th class="sortable" data-sort="montant">Prix unitaire <span class="sort-icon"></span></th>
                        <th class="sortable" data-sort="montant">Prix total <span class="sort-icon"></span></th>
                        <th>Action</th>
                    </tr>
                    <?php
                    // Récupérer les lignes de l'achat actuelle
                    $lignes_achat = getAchatLignes($_GET["id_achat"]);
                    $total_achat = 0;
                    
                    if (!empty($lignes_achat) && is_array($lignes_achat)) {
                        foreach ($lignes_achat as $ligne) {
                            $total_achat += $ligne["prix"];
                            $prix_uni = $ligne["prix"] / $ligne["quantite"]
                    ?>
                    <tr>
                        <td><?= $ligne["nom_article"] ?></td>
                        <td><?= $ligne["quantite"] ?></td>
                        <td><?= $prix_uni ?></td>
                        <td><?= $ligne["prix"] ?></td>
                        <td>
                            <a href="achat.php?id_achat=<?= $_GET['id_achat'] ?>&edit_ligne=<?= $ligne['id'] ?>" title="Modifier" style="color: blue !important; cursor: pointer;"><i class='bx bx-edit-alt'></i></a>
                            <a onclick="supprimerLigne(<?= $ligne['id'] ?>, <?= $_GET['id_achat'] ?>)" title="Supprimer" style="color: red !important; cursor: pointer;"><i class='bx bx-x-circle'></i></a>
                        </td>
                    </tr>
                    <?php
                        }
                    } else {
                    ?>
                    <tr>
                        <td colspan="5" style="text-align: center;">Aucun article dans cette achat</td>
                    </tr>
                    <?php
                    }
                    ?>
                </table>
                <div style="margin-top: 20px; text-align: right; padding-right: 20px;">
                    <strong>Total de l'achat: <?= number_format($total_achat, 2) ?> DZD</strong>
                </div>
            </div>
        </div>
        <?php else: ?>

        <div style="display: block;" class="box">
            <form action="" method="get">
                <table class="mtable">
                    <tr>
                        <th>Fournisseur</th>
                        <th>Montant</th>
                        <th>Date</th>
                    </tr>
                    <tr>
                        <td>
                            <input type="text" name="nom_p_fournisseur" id="nom_p_fournisseur" placeholder="Veuillez saisir le nom ou prenom">
                        </td>
                        <td>
                            <input type="number" name="montant" id="montant" placeholder="Veuillez saisir le montant" min="0" step="any">
                        </td>
                        <td>
                            <input type="date" name="date" id="date">
                        </td>
                    </tr>
                </table>
                <br>
                <button type="submit">Recherche</button>
            </form>
            <br>

            <table class="mtable">
                <tr>
                    <th class="sortable" data-sort="fournisseur">Fournisseur <span class="sort-icon"></span></th>
                    <th class="sortable" data-sort="montant">Montant <span class="sort-icon"></span></th>
                    <th class="sortable" data-sort="date">Date <span class="sort-icon"></span></th>
                    <th>Action</th>
                </tr>
                <?php
                if (!empty($_GET)) {
                    $achats = getAchat(null, $_GET);
                } else {
                    $achats = getAchat();
                }
                if (!empty($achats) && is_array( $achats )) {
                    foreach ($achats as $key => $value) {
                        if ($value["total"] > 0) {
                    ?>
                    <tr>
                        <td><?=$value["nom"]. " ".$value["prenom"] ?></td>
                        <td><?=number_format($value["total"],2,".","")?></td>
                        <td><?=date("d/m/Y H:i:s", strtotime($value["date_achat"]))?></td>
                        <td>
                            <a href="recuAchat.php?id=<?= $value["id"]?>" title="Afficher le Reçu" style="color: blue !important;"><i class='bx bx-receipt'></i></a>
                            <a href="achat.php?id_achat=<?=$value['id']?>" title="Modifier" style="color: blue !important;"><i class='bx bx-edit'></i></a>
                            <a onclick="annuleAchat(<?= $value['id']?>)" title="Annuler" style="color: red; cursor: pointer;"><i class='bx bx-x-circle'></i></a>
                        </td>

                    </tr>    
                <?php
                        }
                    }
                } else {
                ?>
                    <tr>
                        <td colspan="5" style="text-align: center;">Aucun achat</td>
                    </tr>
                <?php
                }
                ?>
            </table>
        </div>
        <?php endif; ?>
    </div>



@endsection

 @section('scripts')

<script>
    function createNewAchat() {
        document.querySelector(".valider").style.display = "none";
        window.location.href = "../model/ajoutAchat.php";
    }

    function annuleAchat(idAchat, idArticle, quantite) {
        if (confirm("Voulez-vous vraiment annuler cette achat?")) {
            window.location.href = "../model/annuleAchat.php?idAchat="+idAchat
        }
    }

    function setPrix() {
        var quantite = document.querySelector("#quantite");
        var prix_u = document.querySelector("#prix_u");
        var prix = document.querySelector("#prix");

        prix.value = Number(quantite.value) * Number(prix_u.value);
    }

    function remplirPrix() {
        var article = document.querySelector("#id_article")
        var prix_u = article.options[article.selectedIndex].getAttribute("data-prix");
        document.querySelector("#prix_u").value = prix_u;
        setPrix();
    }

    function supprimerLigne(idLigne, idAchat) {
        if (confirm("Voulez-vous vraiment supprimer cette ligne?")) {
            window.location.href = "../model/supprimerLigneAchat.php?idLigne=" + idLigne + "&idAchat=" + idAchat;
        }
    }

    // Ensure the price is properly calculated when the page loads (for edit mode)
    window.onload = function() {
        var quantiteField = document.getElementById('quantite');
        if (quantiteField && quantiteField.value !== '') {
            setPrix();
        }
    };

        // Updated JavaScript that changes the sort icon content directly
document.addEventListener('DOMContentLoaded', function() {
    // Get all sortable table headers
    const sortableHeaders = document.querySelectorAll('.sortable');
    
    // Add click event listeners to each sortable header
    sortableHeaders.forEach(header => {
        header.addEventListener('click', function() {
            const table = this.closest('table');
            const tbody = table.querySelector('tbody') || table;
            const rows = Array.from(tbody.querySelectorAll('tr')).slice(1); // Skip the header row
            const columnIndex = Array.from(this.parentNode.children).indexOf(this);
            const sortKey = this.getAttribute('data-sort');
            const sortIcon = this.querySelector('.sort-icon');
            
            // Determine sort direction
            let sortDirection = 'asc';
            if (this.classList.contains('sort-asc')) {
                sortDirection = 'desc';
                this.classList.remove('sort-asc');
                this.classList.add('sort-desc');
                sortIcon.textContent = '▼'; // Update the icon for descending
            } else {
                // Remove any previous sorting classes and reset icons
                sortableHeaders.forEach(h => {
                    h.classList.remove('sort-asc', 'sort-desc');
                    const icon = h.querySelector('.sort-icon');
                    if (icon) icon.textContent = ''; // Reset icon
                });
                this.classList.add('sort-asc');
                sortIcon.textContent = '▲'; // Update the icon for ascending
            }
            
            // Sort the rows
            rows.sort((a, b) => {
                const cellA = a.querySelectorAll('td')[columnIndex].textContent.trim();
                const cellB = b.querySelectorAll('td')[columnIndex].textContent.trim();
                
                // Different sorting logic based on column type
                if (sortKey === 'montant') {
                    // For numeric sorting (remove any currency symbols or formatting)
                    const numA = parseFloat(cellA.replace(/[^0-9.-]+/g, ''));
                    const numB = parseFloat(cellB.replace(/[^0-9.-]+/g, ''));
                    return sortDirection === 'asc' ? numA - numB : numB - numA;
                } else if (sortKey === 'date') {
                    // For date sorting
                    // Convert DD/MM/YYYY H:i:s format to sortable form
                    const datePartsA = cellA.split(' ');
                    const datePartsB = cellB.split(' ');
                    
                    const dayMonthYearA = datePartsA[0].split('/');
                    const dayMonthYearB = datePartsB[0].split('/');
                    
                    // Create sortable date strings (YYYY-MM-DD HH:MM:SS)
                    const sortableDateA = `${dayMonthYearA[2]}-${dayMonthYearA[1]}-${dayMonthYearA[0]} ${datePartsA[1] || '00:00:00'}`;
                    const sortableDateB = `${dayMonthYearB[2]}-${dayMonthYearB[1]}-${dayMonthYearB[0]} ${datePartsB[1] || '00:00:00'}`;
                    
                    // Convert to timestamps for comparison
                    const timeA = new Date(sortableDateA).getTime();
                    const timeB = new Date(sortableDateB).getTime();
                    
                    return sortDirection === 'asc' ? timeA - timeB : timeB - timeA;
                } else {
                    // Default string comparison
                    if (sortDirection === 'asc') {
                        return cellA.localeCompare(cellB);
                    } else {
                        return cellB.localeCompare(cellA);
                    }
                }
            });
            
            // Remove existing rows (except header) and add sorted rows
            rows.forEach(row => row.remove());
            rows.forEach(row => tbody.appendChild(row));
        });
    });
});
</script>

@endsection