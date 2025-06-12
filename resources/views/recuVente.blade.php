@extends('layouts.app')

@section('title', 'Recu Vente - MonStock')
@section('page-title', 'Recu Vente')

@section('content')


        <button class="hidden-print" id="btnPrint" style="position:relative; left:42%;"><i class='bx bx-printer' ></i> Imprimer</button>

    <div class="page">
        <div class="cote-a-cote">
            <h2>MonStock stock</h2>
            <div>
                <p>Reçu N° #: <?= $ventes["id"]?> </p>
                <p>Date: <?= date("d/m/Y H:i:s", strtotime($ventes["date_vente"]))?> </p>
            </div>
        </div>
        <div class="cote-a-cote" style="width: 50%;">
            <p>Nom: </p>
            <p><?=$ventes["nom"]. " ".$ventes["prenom"] ?></p>
        </div>
        <div class="cote-a-cote" style="width: 50%;">
            <p>Téléphone: </p>
            <p><?=$ventes["telephone"]?></p>
        </div>
        <div class="cote-a-cote" style="width: 50%;">
            <p>Adresse: </p>
            <p><?=$ventes["adresse"]?></p>
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
                    
                    $grouped_lignes = [];

                    foreach ($vente_lignes as $ligne) {
                        $prix_unitaire = $ligne["quantite"] != 0 ? $ligne["prix"] / $ligne["quantite"] : 0;

                        $key = $ligne["id_article"] . '_' . number_format($prix_unitaire, 2, ".", "");

                        if (!isset($grouped_lignes[$key])) {
                            $grouped_lignes[$key] = [
                                "nom_article" => $ligne["nom_article"],
                                "quantite" => $ligne["quantite"],
                                "prix_unitaire" => $prix_unitaire,
                                "prix_total" => $ligne["prix"]
                            ];
                        } else {
                            $grouped_lignes[$key]["quantite"] += $ligne["quantite"];
                            $grouped_lignes[$key]["prix_total"] += $ligne["prix"];
                        }
                    }

                    foreach ($grouped_lignes as $ligne) :
                ?>
                <tr>
                    <td><?= $ligne["nom_article"] ?></td>
                    <td><?= $ligne["quantite"] ?></td>
                    <td><?= number_format($ligne["prix_unitaire"], 2, ".", "") ?></td>
                    <td><?= number_format($ligne["prix_total"], 2, ".", "") ?></td>
                </tr>
                <?php endforeach; ?>

  
                <tr>
                    <td colspan="3" style="text-align:right; font-weight:bold;">Total:</td>
                    <td><strong><?=number_format($ventes["total"], 2, ".", "")?> DZD</strong></td>
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