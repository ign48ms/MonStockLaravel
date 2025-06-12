@extends('layouts.app')

@section('title', 'Vente - MonStock')
@section('page-title', 'Vente')

@section('content')


    <?php if (empty($_GET["id_vente"])): ?>
        <button onclick="createNewVente()" class="valider" style="margin-left: 25px; margin-bottom: 5px;">Nouveau Vente</button>
    <?php endif; ?>    
    <div class="overview-boxes">
        <div class="va-container">

            <?php if (!empty($_GET["id_vente"])): ?>
            <div class="vabox">
                <form action="../model/ajoutClientVente.php" method="post" id="vente-form" <?php echo (!empty($_GET["edit_ligne"])) ? 'style="display: none;"' : ''; ?>>
                    <input type="hidden" name="id_vente" value="<?= $_GET['id_vente'] ?>">
                    <label for="id_client">Client</label>
                    <select name="id_client" id="id_client" class="tom-select">
                        <?php 
                            $clients = getClient();
                            if (!empty($clients) && is_array($clients)) {
                                foreach ($clients as $key => $value) {
                                    $selected = (!empty($vente) && $vente["id_client"] == $value["id"]) ? "selected" : "";
                                    echo "<option value='{$value["id"]}' {$selected}>{$value["nom"]} {$value["prenom"]}</option>";
                                }
                            }
                        ?>  
                    </select>
                    <button class="valider" type="submit">Valider Vente</button>
                </form>

                <div class="vabox">
                    <form action="<?php echo (!empty($_GET["edit_ligne"])) ? '../model/modifVenteLigne.php' : '../model/ajoutArticleVente.php'; ?>" method="post" id="article-form">
                        <input type="hidden" name="id_vente" value="<?= $_GET['id_vente'] ?>">
                            
                        <?php 
                            $has_form_values = isset($_SESSION["form_values"]);

                            if (!empty($_GET["edit_ligne"])): 
                            // Fetch the ligne data for editing
                            $lignes = getVenteLignes($_GET["id_vente"]);
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
                            <option value="" disabled <?php echo (empty($_GET["edit_ligne"]) && !$has_form_values) ? 'selected' : ''; ?>>Choisir un article</option>
                            <?php 
                                $articles = getArticle();
                                if (!empty($articles) && is_array($articles)) {
                                    foreach ($articles as $key => $value) {
                                        // Check if we're in edit mode, have form values, or this is the selected article
                                        $selected = '';
                                        if (!empty($edit_ligne) && $edit_ligne["id_article"] == $value["id"]) {
                                            $selected = 'selected';
                                        } elseif ($has_form_values && $_SESSION["form_values"]["id_article"] == $value["id"]) {
                                            $selected = 'selected';
                                        }
                                        echo "<option data-prix='{$value["prix_vente_unitaire"]}' value='{$value["id"]}' {$selected}>{$value["nom_article"]} - {$value["quantite"]} disponible</option>";                                   
                                    }
                                }
                            ?>  
                        </select>

                        <label for="quantite">Quantité</label>
                        <input onkeyup="setPrix()" type="number" name="quantite" id="quantite" placeholder="Veuillez saisir la quantité" min="1" 
                            value="<?php 
                                   if (!empty($edit_ligne)) {
                                       echo $edit_ligne["quantite"];
                                   } elseif ($has_form_values) {
                                       echo $_SESSION["form_values"]["quantite"];
                                   } 
                               ?>">

                        <label for="prix_u">Prix unitaire</label>
                        <input onkeyup="setPrix()" type="number" name="prix_u" id="prix_u" placeholder="Prix unitaire" min="0" step="any" 
                            value="<?php 
                                   if (!empty($edit_ligne)) {
                                       echo ($edit_ligne["prix"] / $edit_ligne["quantite"]);
                                   } elseif ($has_form_values) {
                                       echo $_SESSION["form_values"]["prix_u"];
                                   }
                               ?>">
                        
                        <label for="prix">Prix total</label>
                        <input type="number" name="prix" id="prix" placeholder="Veuillez saisir le prix" min="0" step="any" 
                            value="<?php 
                                   if (!empty($edit_ligne)) {
                                       echo $edit_ligne["prix"];
                                   } elseif ($has_form_values) {
                                       echo $_SESSION["form_values"]["prix"];
                                   }
                               ?>">
                        
                        <button type="submit"><?php echo (!empty($_GET["edit_ligne"])) ? 'Modifier' : 'Ajouter'; ?></button>
                        <?php if (!empty($_GET["edit_ligne"])): ?>
                            <button type="button" onclick="window.location.href='vente.php?id_vente=<?= $_GET['id_vente'] ?>'" class="valider">Annuler</button>
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

                        if ($has_form_values) {
                            unset($_SESSION["form_values"]);
                        }
                        ?>
                    </form>
                </div>
            </div>

            <div style="display: block;" class="box">
                    <h3>Détails de la vente</h3>
                    <table id="details-vente-table" class="sortable-table mtable">
                        <tr>
                            <th class="sortable" data-sort="article">Article <span class="sort-icon"></span></th>
                            <th class="sortable" data-sort="montant">Quantité <span class="sort-icon"></span></th>
                            <th class="sortable" data-sort="montant">Prix unitaire <span class="sort-icon"></span></th>
                            <th class="sortable" data-sort="montant">Prix total <span class="sort-icon"></span></th>
                            <th>Action</th>
                        </tr>
                        <?php
                        // Récupérer les lignes de la vente actuelle
                        $lignes_vente = getVenteLignes($_GET["id_vente"]);
                        $total_vente = 0;
                        
                        if (!empty($lignes_vente) && is_array($lignes_vente)) {
                            foreach ($lignes_vente as $ligne) {
                                $total_vente += $ligne["prix"];
                                $prix_uni = $ligne["prix"] / $ligne["quantite"]
                        ?>
                        <tr>
                            <td><?= $ligne["nom_article"] ?></td>
                            <td><?= $ligne["quantite"] ?></td>
                            <td><?= $prix_uni ?></td>
                            <td><?= $ligne["prix"] ?></td>
                            <td>
                                <a href="vente.php?id_vente=<?= $_GET['id_vente'] ?>&edit_ligne=<?= $ligne['id'] ?>" title="Modifier" style="color: blue !important; cursor: pointer;"><i class='bx bx-edit-alt'></i></a>
                                <a onclick="supprimerLigne(<?= $ligne['id'] ?>, <?= $_GET['id_vente'] ?>)" title="Supprimer" style="color: red !important; cursor: pointer;"><i class='bx bx-x-circle'></i></a>
                            </td>
                        </tr>
                        <?php
                            }
                        } else {
                        ?>
                        <tr>
                            <td colspan="5" style="text-align: center;">Aucun article dans cette vente</td>
                        </tr>
                        <?php
                        }
                        ?>
                    </table>
                    <div style="margin-top: 20px; text-align: right; padding-right: 20px;">
                        <strong>Total de la vente: <?= number_format($total_vente, 2) ?> DZD</strong>
                    </div>
                </div>
            </div>
            <?php else: ?>


            <div style="display: block;" class="box">
                <form action="" method="get">
                    <table class="mtable">
                        <tr>
                            <th>Client</th>
                            <th>Montant</th>
                            <th>Date</th>
                        </tr>
                        <tr>
                            <td>
                                <input type="text" name="nom_p_client" id="nom_p_client" placeholder="Veuillez saisir le nom ou prenom">
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
                        
                <table id="ventes-table" class="sortable-table mtable">
                    <tr>
                        <th class="sortable" data-sort="client">Client <span class="sort-icon"></span></th>
                        <th class="sortable" data-sort="montant">Montant <span class="sort-icon"></span></th>
                        <th class="sortable" data-sort="date">Date <span class="sort-icon"></span></th>
                        <th>Action</th>
                    </tr>
                    <?php
                    if (!empty($_GET)) {
                        $ventes = getVente(null, $_GET);
                    } else {
                        $ventes = getVente();
                    }
                    if (!empty($ventes) && is_array( $ventes )) {
                        foreach ($ventes as $key => $value) {
                            if ($value["total"] > 0) {
                        ?>
                        <tr>                      
                            <td><?=$value["nom"]. " ".$value["prenom"] ?></td>
                            <td><?=number_format($value["total"],2,".","")?></td>
                            <td><?=date("d/m/Y H:i:s", strtotime($value["date_vente"]))?></td>
                            <td>
                                <a href="recuVente.php?id=<?= $value["id"]?>" title="Afficher le Reçu" style="color: blue !important;"><i class='bx bx-receipt'></i></a>
                                <a href="vente.php?id_vente=<?=$value['id']?>" title="Modifier" style="color: blue !important;"><i class='bx bx-edit'></i></a>
                                <a onclick="annuleVente(<?= $value['id']?>)" title="Annuler" style="color: red; cursor: pointer;"><i class='bx bx-x-circle'></i></a>
                            </td>

                        </tr>    
                    <?php
                            }
                        }
                    }else {
                    ?>
                        <tr>
                            <td colspan="5" style="text-align: center;">Aucun vente</td>
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

    function createNewVente() {
        document.querySelector(".valider").disabled = true;
        window.location.href = "../model/ajoutVente.php";
    }
        
    function annuleVente(idVente, idArticle, quantite) {
        if (confirm("Voulez-vous vraiment annuler cette vente?")) {
            window.location.href = "../model/annuleVente.php?idVente="+idVente
        }
    }

    function setPrix() {
        var quantite = document.querySelector("#quantite");
        var prix_u = document.querySelector("#prix_u");
        var prix = document.querySelector("#prix");

        
        prix.value = Number(quantite.value) * Number(prix_u.value);
    }

    function remplirPrix(){
        var article = document.querySelector("#id_article")
        var prix_u = article.options[article.selectedIndex].getAttribute("data-prix");
        document.querySelector("#prix_u").value = prix_u;
        setPrix();
    }

    function supprimerLigne(idLigne, idVente) {
        if (confirm("Voulez-vous vraiment supprimer cette ligne?")) {
            window.location.href = "../model/supprimerLigneVente.php?idLigne=" + idLigne + "&idVente=" + idVente;
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