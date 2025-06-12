<?php

use Illuminate\Support\Facades\DB;

// Helper function to get Laravel's PDO connection
function getDbConnection() {
    return DB::connection()->getPdo();
}

function getArticle($id=null, $DONNErecherche = array()) {
    $connexion = getDbConnection();
    
    if (!empty($id)) {
        $sql = "SELECT nom_article, libelle_categorie, quantite, prix_vente_unitaire, prix_achat_unitaire, date_fabrication,
        date_expiration, id_categorie, a.id FROM article AS a, categorie_article AS c
        WHERE a.id_categorie = c.id AND a.id=? AND a.etat=?";

        $req = $connexion->prepare($sql);
        $req->execute(array($id,1));
        return $req->fetch();

    } elseif (!empty($DONNErecherche)) {
        $recherche = "";
        extract($DONNErecherche);
        if (!empty($nom_article)) $recherche .= "AND a.nom_article LIKE '%$nom_article%' ";
        if (!empty($id_categorie)) $recherche .= " AND a.id_categorie = $id_categorie ";
        if (!empty($quantite)) $recherche .= " AND a.quantite = $quantite ";
        if (!empty($prix_vente_unitaire)) $recherche .= " AND a.prix_vente_unitaire = $prix_vente_unitaire ";
        if (!empty($prix_achat_unitaire)) $recherche .= " AND a.prix_achat_unitaire = $prix_achat_unitaire ";
        if (!empty($date_fabrication)) $recherche .= " AND DATE(a.date_fabrication) = '$date_fabrication' ";
        if (!empty($date_expiration)) $recherche .= " AND DATE(a.date_expiration) = '$date_expiration' ";

        $sql = "SELECT nom_article, libelle_categorie, quantite, prix_vente_unitaire, prix_achat_unitaire, date_fabrication,
        date_expiration, id_categorie, a.id FROM article AS a, categorie_article AS c
        WHERE a.id_categorie = c.id $recherche";

        $req = $connexion->prepare($sql);
        $req->execute();
        return $req->fetchAll();
    } else {
        $sql = "SELECT nom_article, libelle_categorie, quantite, prix_vente_unitaire, prix_achat_unitaire, date_fabrication,
        date_expiration, id_categorie, a.id FROM article AS a, categorie_article AS c
        WHERE a.id_categorie = c.id AND a.etat=?";

        $req = $connexion->prepare($sql);
        $req->execute(array(1));
        return $req->fetchAll();
    }
}

function getClient($id=null, $DONNErecherche = array()) {
    $connexion = getDbConnection();
    
    if (!empty($id)) {
        $sql = "SELECT * FROM client WHERE id=? AND etat=?";
        $req = $connexion->prepare($sql);
        $req->execute(array($id,1));
        return $req->fetch();

    } elseif(!empty($DONNErecherche)) {
        $recherche= "";
        extract($DONNErecherche);
        if (!empty($nom)) $recherche .= "AND nom LIKE '%$nom%' ";
        if (!empty($prenom)) $recherche .= "AND prenom LIKE '%$prenom%' ";
        if (!empty($telephone)) $recherche .= "AND telephone LIKE '%$telephone%' ";
        if (!empty($adresse)) $recherche .= "AND adresse LIKE '%$adresse%' ";

        $sql = "SELECT * FROM client WHERE etat='1' $recherche";
        $req = $connexion->prepare($sql);
        $req->execute();
        return $req->fetchAll();
    } else {
        $sql = "SELECT * FROM client WHERE etat=?";
        $req = $connexion->prepare($sql);
        $req->execute(array(1));
        return $req->fetchAll();
    }
}

function getVente($id=null, $DONNErecherche = array()) {
    $connexion = getDbConnection();
    
    if (!empty($id)) {
        $sql = "SELECT v.id, nom, prenom, total, date_vente, adresse, telephone
         FROM client AS c, vente AS v WHERE v.id_client=c.id AND v.id=? AND v.etat=? ORDER BY v.date_vente ASC";

        $req = $connexion->prepare($sql);
        $req->execute(array($id,1));
        return $req->fetch();

    } elseif (!empty($DONNErecherche)) {
        $params = array(1); // Start with etat=1 parameter
        $recherche = "";
        
        if (!empty($DONNErecherche['nom_p_client'])) {
            // Split the search term into parts
            $search_terms = explode(' ', trim($DONNErecherche['nom_p_client']));
            
            if (count($search_terms) > 1) {
                // If there are multiple terms, handle them separately
                $recherche .= " AND (";
                
                // Option 1: First term matches nom, second term matches prenom (San(nom) Salvador(prenom))
                $recherche .= "(c.nom LIKE ? AND c.prenom LIKE ?)";
                $params[] = '%' . $search_terms[0] . '%';
                $params[] = '%' . $search_terms[1] . '%';
                
                // Option 2: First term matches prenom, second term matches nom (Salvador(prenom) San(nom))
                $recherche .= " OR (c.prenom LIKE ? AND c.nom LIKE ?)";
                $params[] = '%' . $search_terms[0] . '%';
                $params[] = '%' . $search_terms[1] . '%';
                
                // Option 3: Full search term matches either field (San Salvador(nom) San Salvador(prenom))
                $recherche .= " OR c.nom LIKE ? OR c.prenom LIKE ?";
                $params[] = '%' . $DONNErecherche['nom_p_client'] . '%';
                $params[] = '%' . $DONNErecherche['nom_p_client'] . '%';
                
                $recherche .= ")";
            } else {
                // Single search term, keep original logic (if there is only 1 term)
                $recherche .= " AND (c.nom LIKE ? OR c.prenom LIKE ?) ";
                $params[] = '%' . $DONNErecherche['nom_p_client'] . '%';
                $params[] = '%' . $DONNErecherche['nom_p_client'] . '%';
            }
        }
        
        if (!empty($DONNErecherche['montant'])) {
            $recherche .= " AND v.total = ? ";
            $params[] = floatval($DONNErecherche['montant']);
        }
        
        if (!empty($DONNErecherche['date'])) {
            $recherche .= " AND DATE(v.date_vente) = ? ";
            $params[] = $DONNErecherche['date'];
        }
        
        $sql = "SELECT v.id, nom, prenom, total, date_vente
                FROM client AS c, vente AS v
                WHERE v.id_client = c.id AND v.etat = ?" . $recherche . " ORDER BY v.date_vente ASC";
        
        $req = $connexion->prepare($sql);
        $req->execute($params);
        return $req->fetchAll();
    } else {   
        $sql = "SELECT v.id, nom, prenom, total, date_vente
                FROM client AS c, vente AS v WHERE v.id_client=c.id AND v.etat=? ORDER BY v.date_vente ASC";

        $req = $connexion->prepare($sql);
        $req->execute(array(1));
        return $req->fetchAll();
    }
}

function getFournisseur($id=null, $DONNErecherche = array()) {
    $connexion = getDbConnection();
    
    if (!empty($id)) {
        $sql = "SELECT * FROM fournisseur WHERE id=? AND etat=?";
        $req = $connexion->prepare($sql);
        $req->execute(array($id,1));
        return $req->fetch();

    } elseif(!empty($DONNErecherche)) {
        $recherche= "";
        extract($DONNErecherche);
        if (!empty($nom)) $recherche .= "AND nom LIKE '%$nom%' ";
        if (!empty($prenom)) $recherche .= "AND prenom LIKE '%$prenom%' ";
        if (!empty($telephone)) $recherche .= "AND telephone LIKE '%$telephone%' ";
        if (!empty($adresse)) $recherche .= "AND adresse LIKE '%$adresse%' ";

        $sql = "SELECT * FROM fournisseur WHERE etat='1' $recherche";
        $req = $connexion->prepare($sql);
        $req->execute();
        return $req->fetchAll(); 

    } else {
        $sql = "SELECT * FROM fournisseur WHERE etat=?";
        $req = $connexion->prepare($sql);
        $req->execute(array(1));
        return $req->fetchAll();
    }
}

function getAchat($id=null, $DONNErecherche = array()) {
    $connexion = getDbConnection();
    
    if (!empty($id)) {
        $sql = "SELECT a.id, nom, prenom, total, date_achat, adresse, telephone
         FROM fournisseur AS f, achat AS a WHERE a.id_fournisseur=f.id AND a.id=? AND a.etat=? ORDER BY a.date_achat ASC";

        $req = $connexion->prepare($sql);
        $req->execute(array($id,1));
        return $req->fetch();

    } elseif (!empty($DONNErecherche)) {
        $params = array(1); // Start with etat=1 parameter
        $recherche = "";
        
        if (!empty($DONNErecherche['nom_p_fournisseur'])) {
            // Split the search term into parts
            $search_terms = explode(' ', trim($DONNErecherche['nom_p_fournisseur']));
            
            if (count($search_terms) > 1) {
                // If there are multiple terms, handle them separately
                $recherche .= " AND (";
                
                // Option 1: First term matches nom, second term matches prenom (San(nom) Salvador(prenom))
                $recherche .= "(f.nom LIKE ? AND f.prenom LIKE ?)";
                $params[] = '%' . $search_terms[0] . '%';
                $params[] = '%' . $search_terms[1] . '%';
                
                // Option 2: First term matches prenom, second term matches nom (Salvador(prenom) San(nom))
                $recherche .= " OR (f.prenom LIKE ? AND f.nom LIKE ?)";
                $params[] = '%' . $search_terms[0] . '%';
                $params[] = '%' . $search_terms[1] . '%';
                
                // Option 3: Full search term matches either field (San Salvador(nom) San Salvador(prenom))
                $recherche .= " OR f.nom LIKE ? OR f.prenom LIKE ?";
                $params[] = '%' . $DONNErecherche['nom_p_fournisseur'] . '%';
                $params[] = '%' . $DONNErecherche['nom_p_fournisseur'] . '%';
                
                $recherche .= ")";
            } else {
                // Single search term, keep original logic (if there is only 1 term)
                $recherche .= " AND (f.nom LIKE ? OR f.prenom LIKE ?) ";
                $params[] = '%' . $DONNErecherche['nom_p_fournisseur'] . '%';
                $params[] = '%' . $DONNErecherche['nom_p_fournisseur'] . '%';
            }
        }
        
        if (!empty($DONNErecherche['montant'])) {
            $recherche .= " AND a.total = ? ";
            $params[] = floatval($DONNErecherche['montant']);
        }
        
        if (!empty($DONNErecherche['date'])) {
            $recherche .= " AND DATE(a.date_achat) = ? ";
            $params[] = $DONNErecherche['date'];
        }
        
        $sql = "SELECT a.id, nom, prenom, total, date_achat
                FROM fournisseur AS f, achat AS a
                WHERE a.id_fournisseur = f.id AND a.etat = ?" . $recherche . " ORDER BY a.date_achat ASC";
        
        $req = $connexion->prepare($sql);
        $req->execute($params);
        return $req->fetchAll();
    } else {
        $sql = "SELECT a.id, nom, prenom, total, date_achat
                FROM fournisseur AS f, achat AS a WHERE a.id_fournisseur=f.id AND a.etat=? ORDER BY a.date_achat ASC";

        $req = $connexion->prepare($sql);
        $req->execute(array(1));
        return $req->fetchAll();
    }
}

function getAllAchat() {
    $connexion = getDbConnection();
    $sql = "SELECT COUNT(*) AS nbre FROM achat WHERE etat=?";
    $req = $connexion->prepare($sql);
    $req->execute(array(1));
    return $req->fetch();
}

function getAllVente() {
    $connexion = getDbConnection();
    $sql = "SELECT COUNT(*) AS nbre FROM vente WHERE etat=?";
    $req = $connexion->prepare($sql);
    $req->execute(array(1));
    return $req->fetch();
}

function getAllArticle() {
    $connexion = getDbConnection();
    $sql = "SELECT COUNT(*) AS nbre FROM article";
    $req = $connexion->prepare($sql);
    $req->execute();
    return $req->fetch();
}

function getCA() {
    $connexion = getDbConnection();
    $sql = "SELECT SUM(total) AS total FROM vente";
    $req = $connexion->prepare($sql);
    $req->execute();
    return $req->fetch();
}

function getLastVente($id = null) {
    $connexion = getDbConnection();
    $sql = "SELECT v.id, nom, prenom, total, date_vente
            FROM vente v, client c WHERE v.id_client = c.id AND v.etat = ? ORDER BY date_vente DESC LIMIT 10 "; 
            
    $req = $connexion->prepare($sql);
    $req->execute([1]);
    return $req->fetchAll();
}

function getMostVente($id=null) {
    $connexion = getDbConnection();
    $sql = "SELECT a.nom_article, SUM(vl.prix) AS prix
            FROM vente v, vente_ligne vl, article a WHERE v.id = vl.id_vente AND vl.id_article = a.id AND v.etat = ?
            GROUP BY a.id, a.nom_article ORDER BY prix DESC LIMIT 10";

    $req = $connexion->prepare($sql);
    $req->execute([1]);
    return $req->fetchAll();
}

function getCategorie($id=null) {
    $connexion = getDbConnection();
    
    if (!empty($id)) {
        $sql = "SELECT * FROM categorie_article WHERE id=? AND etat=?";
        $req = $connexion->prepare($sql);
        $req->execute(array($id, 1));
        return $req->fetch();
    } else {
        $sql = "SELECT * FROM categorie_article WHERE etat=?";
        $req = $connexion->prepare($sql);
        $req->execute(array(1));
        return $req->fetchAll();
    }
}

function getVenteLignes($idVente) {
    $connexion = getDbConnection();
    $sql = "SELECT vl.*, a.nom_article, a.prix_vente_unitaire
            FROM vente_ligne vl, article a WHERE vl.id_article = a.id AND vl.id_vente = ? ORDER BY vl.id ASC";
  
    $req = $connexion->prepare($sql);
    $req->execute([$idVente]);
    return $req->fetchAll(PDO::FETCH_ASSOC);
}

function getAchatLignes($idAchat) {
    $connexion = getDbConnection();
    $sql = "SELECT al.*, a.nom_article, a.prix_achat_unitaire
            FROM achat_ligne al, article a WHERE al.id_article = a.id AND al.id_achat = ? ORDER BY al.id ASC";

    $req = $connexion->prepare($sql);
    $req->execute([$idAchat]);
    return $req->fetchAll(PDO::FETCH_ASSOC);
}

function LowStock(){
    $connexion = getDbConnection();
    $sql = "SELECT COUNT(*) as nb FROM article WHERE quantite < 5";
    $req = $connexion->query($sql);
    $res = $req->fetch();
    return $res['nb'] > 0; 
}

?>