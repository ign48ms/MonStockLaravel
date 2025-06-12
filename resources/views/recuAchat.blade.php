@extends('layouts.app')

@section('title', 'Recu Achat - MonStock')
@section('page-title', 'Recu Achat')

@section('content')


        <button class="hidden-print" id="btnPrint" style="position:relative; left:42%;"><i class='bx bx-printer' ></i> Imprimer</button>

    <div class="page">
        <div class="cote-a-cote">
            <h2>MonStock stock</h2>
            <div>
                <p>Reçu N° #: <?= $achats["id"]?> </p>
                <p>Date: <?= date("d/m/Y H:i:s", strtotime($achats["date_achat"]))?> </p>
            </div>
        </div>
        <div class="cote-a-cote" style="width: 50%;">
            <p>Nom: </p>
            <p><?=$achats["nom"]. " ".$achats["prenom"] ?></p>
        </div>
        <div class="cote-a-cote" style="width: 50%;">
            <p>Téléphone: </p>
            <p><?=$achats["telephone"]?></p>
        </div>
        <div class="cote-a-cote" style="width: 50%;">
            <p>Adresse: </p>
            <p><?=$achats["adresse"]?></p>
        </div>

        <br>

        <table class="mtable">
                <tr>
                    <th>Produit</th>
                    <th>Quantité</th>
                    <th>Prix unitaire</th>
                    <th>Montant</th>
                </tr>
                <?php
                // Regrouper les lignes par article et prix
                $grouped_lignes = [];

                foreach ($achat_lignes as $ligne) {
                    $key = $ligne["id_article"] . "_" . $ligne["prix"]; // Group by both article and prix
                    if (!isset($grouped_lignes[$key])) {
                        $grouped_lignes[$key] = [
                            "nom_article" => $ligne["nom_article"],
                            "quantite" => $ligne["quantite"],
                            "prix" => $ligne["prix"],
                        ];
                    } else {
                        $grouped_lignes[$key]["quantite"] += $ligne["quantite"];
                        $grouped_lignes[$key]["prix"] += $ligne["prix"];
                    }
                }
                ?>

                <?php foreach ($grouped_lignes as $ligne) : ?>
                <tr>
                    <td><?= $ligne["nom_article"] ?></td>
                    <td><?= $ligne["quantite"] ?></td>
                    <td>
                        <?php 
                            if ($ligne["quantite"] != 0) {
                                $prix_unitaire = $ligne["prix"] / $ligne["quantite"];
                            } else {
                                $prix_unitaire = 0;
                            }
                        ?>
                        <?= number_format($prix_unitaire, 2, ".", "") ?>
                    </td>
                    <td><?= number_format($ligne["prix"], 2, ".", "") ?></td>
                </tr>
                <?php endforeach; ?>    
                <tr>
                    <td colspan="3" style="text-align:right; font-weight:bold;">Total:</td>
                    <td><strong><?= number_format($achats["total"],2,".","") ?> DZD</strong></td>
                </tr>
        </table>
    </div>
    
@endsection

@section('scripts')

<script>
    var btnPrint =document.querySelector("#btnPrint");
    btnPrint.addEventListener("click", () => {
       window.print(); 
    });

    
</script>

@endsection