<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $table = 'article';

    protected $fillable = [
        'nom_article',
        'id_categorie',
        'quantite',
        'prix_vente_unitaire',
        'prix_achat_unitaire',
        'date_fabrication',
        'date_expiration',
    ];

    public $timestamps = false;

    public function categorie(){
        return $this->belongsTo(Categorie::class, 'id_categorie', 'id');
    }
}
