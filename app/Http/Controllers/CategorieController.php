<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categorie;

class CategorieController extends Controller
{
    public function index(Request $request){

        $categories = Categorie::all();

        
        $categorie = null;
        if ($request->filled('id')) {
            $categorie = Categorie::find($request->id);
        }

        return view('categorie', compact('categories', 'categorie'));
    }

    public function store(Request $request) {
        // 1. Validate the input
        $validated = $request->validate([
            'libelle_categorie' => 'required|string|max:255'
        ]);
        
        try {
            // 2. Create the category
            Categorie::create($validated);
            
            // 3. Redirect with success
            return redirect()->route('categorie')->with('success', 'Catégorie ajouté avec succès!');
            
        } catch (\Exception $e) {
            // 4. Handle errors
            return redirect()->route('categorie')->with('error', 'Erreur lors de l\'ajout de la catégorie: ' . $e->getMessage());
        }
    }

    public function update(Request $request) {
        $validated = $request->validate([
            'id' => 'required|exists:categorie_article,id',
            'libelle_categorie' => 'required|string|max:255'
        ]);
        
        try {
            $categorie = Categorie::find($validated['id']);  // What should go here?
            $categorie->update($validated);  // What should go here?
            
            return redirect()->route('categorie')->with('success', 'Catégorie modifié avec succès!');  // Success message
            
        } catch (\Exception $e) {
            return redirect()->route('categorie')->with('error', 'Erreur lors de la modification: ' . $e->getMessage());  // Error message
        }
    }

    public function destroy(Request $request) {
        try {
            $categorie = Categorie::find($request->idCategorie);  // What parameter should you use?
            
            if (!$categorie) {
                return redirect()->route('categorie')->with('error', 'Catégorie non trouvé!');
            }
            
            $categorie->delete();
            
            return redirect()->route('categorie')->with('success', 'Catégorie supprimé avec succès!');  // Success message
            
        } catch (\Exception $e) {
            return redirect()->route('categorie')->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());  // Error message
        }
    }
}
