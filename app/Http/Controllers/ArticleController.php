<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Categorie;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    // Display the articles pages with form and tables

    public function index(Request $request){

        $categories = Categorie::all();

        $query = Article::with('categorie');

        // Apply filters if they exist
        if ($request->filled('nom_article')) {
            $query->where('nom_article', 'like', '%' . $request->nom_article . '%');
        }

        if ($request->filled('id_categorie')) {
            $query->where('id_categorie', $request->id_categorie );
        }

        if ($request->filled('quantite')) {
            $query->where('quantite', $request->quantite );
        }

        if ($request->filled('prix_vente_unitaire')) {
            $query->where('prix_vente_unitaire', $request->prix_vente_unitaire );
        }

        if ($request->filled('prix_achat_unitaire')) {
            $query->where('prix_achat_unitaire', $request->prix_achat_unitaire );
        }

        if ($request->filled('date_fabrication')) {
            $query->whereDate('date_fabrication', $request->date_fabrication );
        }
        
        if ($request->filled('date_expiration')) {
            $query->whereDate('date_expiration', $request->date_expiration );
        }

        $articles = $query->get();

        $article = null;
        if($request->filled('id')){
            $article = Article::find($request->id);
        }

        return view('article', compact('articles', 'categories', 'article'));

    }

    //Store new articles

    public function store(Request $request){

        $validated = $request->validate([
            'nom_article' => 'required|string|max:255',
            'id_categorie' => 'required|exists:categorie_table,id',
            'quantite' => 'nullable|integer|min:0',
            'prix_vente_unitaire' => 'nullable|numeric|min:0',
            'prix_achat_unitaire' => 'nullable|numeric|min:0',
            'date_fabrication' => 'nullable|date',
            'date_expiration' => 'nullable|date',
        ]);

        try{

            //Create new article
            Article::create($validated);

            // Redirect with success message
            return redirect()->route('article')->with('success', 'Article ajouté avec succès!');

        } catch (\Exception $e){

            // Redirect with error message
            return redirect()->route('article')->with('error', 'Erreur lors de l\'ajout de l\'article: ' . $e->getMessage());

        }
    }

    //Update Article
    public function update(Request $request){

        $validated = $request->validate([
            'id' => 'required|exists:article,id',
            'nom_article' => 'required|string|max:255',
            'id_categorie' => 'required|exists:categorie_table,id',
            'quantite' => 'nullable|integer|min:0',
            'prix_vente_unitaire' => 'nullable|numeric|min:0',
            'prix_achat_unitaire' => 'nullable|numeric|min:0',
            'date_fabrication' => 'nullable|date',
            'date_expiration' => 'nullable|date'
        ]);

        try {

            $article = Article::find($validated['id']);
            $article->update($validated);
            
            return redirect()->route('article')->with('success', 'Article modifié avec succès!');

        } catch (\Exception $e) {

            return redirect()->route('article')->with('error', 'Erreur lors de la modification: ' . $e->getMessage());

        }
    }

    //Delete Article
    public function destroy(Request $request)
    {
        try {

            $article = Article::find($request->idArticle);
            if (!$article) {
                return redirect()->route('article')->with('error', 'Article non trouvé!');
            }
            
            $article->delete();
            
            return redirect()->route('article')->with('success', 'Article supprimé avec succès!');

        } catch (\Exception $e) {

            return redirect()->route('article')->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());

        }
    }
}
