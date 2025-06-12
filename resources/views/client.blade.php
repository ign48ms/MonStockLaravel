<?php 
    include("entete.php");

    if (!empty($_GET["id"])) {
        $client = getClient($_GET["id"]);
    }
?>

<div class="home-content">
    <div class="overview-boxes">
        <div class="vabox">
            <form action=" <?= !empty($_GET["id"]) ? "../model/modifClient.php" : "../model/ajoutClient.php" ?>" method="post">
                <h3><?= !empty($_GET["id"]) ? "Modifier Client" : "Ajouter un Client" ?></h3>
                <label for="nom">Nom</label>
                <input value="<?= !empty($_GET["id"]) ? $client["nom"] : "" ?>" type="text" name="nom" id="nom" placeholder="Veuillez saisir le nom">
                <input value="<?= !empty($_GET["id"]) ? $client["id"] : "" ?>" type="hidden" name="id" id="id">

                <label for="prenom">Prénom</label>
                <input value="<?= !empty($_GET["id"]) ? $client["prenom"] : "" ?>" type="text" name="prenom" id="prenom" placeholder="Veuillez saisir le prénom">

                <label for="telephone">N° de Téléphone</label>
                <input value="<?= !empty($_GET["id"]) ? $client["telephone"] : "" ?>" type="text" name="telephone" id="telephone" placeholder="Veuillez saisir le N° de téléphone">

                <label for="adresse">Adresse</label>
                <input value="<?= !empty($_GET["id"]) ? $client["adresse"] : "" ?>" type="text" name="adresse" id="adresse" placeholder="Veuillez saisir l'adresse">

                <button type="submit">Valider</button>
                
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
        <div style="display: block;" class="box">
            <form action="" method="get">
                <table class="mtable">
                    <tr>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>N° Téléphone</th>
                        <th>Adresse</th>
                    </tr>
                    <tr>
                        <td>
                            <input type="text" name="nom" id="nom" placeholder="Veuillez saisir le nom">
                        </td>
                        <td>
                            <input type="text" name="prenom" id="prenom" placeholder="Veuillez saisir le prénom">
                        </td>
                        <td>
                            <input type="text" name="telephone" id="telephone" placeholder="Veuillez saisir le N° Téléphone">
                        </td>
                        <td>
                            <input type="text" name="adresse" id="adresse" placeholder="Veuillez saisir l'Adresse">
                        </td>
                    </tr>
                </table>
                <br>
                <button type="submit">Recherche</button>
            </form>
            <br>
            <table class="mtable">
                <tr>
                    <th class="sortable" data-sort="nom">Nom <span class="sort-icon"></span></th>
                    <th class="sortable" data-sort="prenom">Prénom <span class="sort-icon"></span></th>
                    <th class="sortable" data-sort="telephone">N° de Téléphone <span class="sort-icon"></span></th>
                    <th class="sortable" data-sort="adresse">Adresse <span class="sort-icon"></span></th>
                    <th>Action</th>
                </tr>
                <?php
                    if (!empty($_GET)) {
                        $clients = getClient(null, $_GET);
                    } else {
                        $clients = getClient();
                    }

                    if (!empty($clients) && is_array( $clients )) {
                        foreach ($clients as $key => $value) {
                    ?>
                    <tr>
                        <td><?=$value["nom"]?></td>
                        <td><?=$value["prenom"]?></td>
                        <td><?=$value["telephone"]?></td>
                        <td><?=$value["adresse"]?></td>
                        <td>
                            <a href="?id=<?=$value['id']?>" title="Modifier" style="color: blue !important;"><i class='bx bx-edit-alt'></i></a>
                            <a onclick="supprimeClient(<?= $value['id']?>)" title="Supprimer" style="color: red; cursor: pointer;"><i class='bx bx-x-circle'></i></a>
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
    function supprimeClient(idClient) {

    if (confirm("Voulez-vous vraiment supprimer ce client?")) {
        window.location.href = `../model/supprimeClient.php?idClient=${idClient}`;
    }
}

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