<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categorie extends Model
{
    use HasFactory;

    protected $table = 'categorie_article';
    protected $fillable = ['libelle_categorie'];
    public $timestamps = false;

    public function articles(){
        return $this->hasMany(Article::class, 'id_categorie', 'id');
    }
}
