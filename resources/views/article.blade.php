@extends('layouts.app')

@section('title', 'Article - MonStock')
@section('page-title', 'Article')

@section('content')


    <div class="overview-boxes">
        <div class="vabox">

            <form action="{{ $article ? route('article.update') : route('article.store') }}" method="post">

                @csrf

                <h3>{{ $article ? 'Modifier Article' : 'Ajouter Article' }}</h3>

                @if ($article)
                    <input type="hidden" name="id" value="{{ $article->id }}">
                @endif


                <label for="nom_article">Nom de l'article</label>
                <input value="{{ $article->nom_article ?? old('nom_article') }}" type="text" name="nom_article" id="nom_article" placeholder="Veuillez saisir le nom" required>


                <label for="id_categorie">Catégorie</label>
                <select name="id_categorie" id="id_categorie" class="tom-select" required>
                    <option value="" disabled selected>Choisir une Catégorie</option>
                    @foreach ($categories as $categorie)
                        <option value="{{ $categorie->id }}" 
                                {{ ($article && $article->id_categorie == $categorie->id) || old('id_categorie') == $categorie->id ? 'selected' : '' }}>
                                {{ $categorie->libelle_categorie }}
                        </option>
                    @endforeach
                </select>


                <label for="quantite">Quantité</label>
                <input value="{{ $article->quantite ?? old('quantite') }}" type="number" name="quantite" id="quantite" placeholder="Veuillez saisir la quantité" min="0">


                <label for="prix_vente_unitaire">Prix de vente unitaire</label>
                <input value="{{ $article->prix_vente_unitaire ?? old('prix_vente_unitaire') }}" type="number" name="prix_vente_unitaire" id="prix_vente_unitaire" placeholder="Veuillez saisir le prix" min="0" step="any">


                <label for="prix_achat_unitaire">Prix d'achat unitaire</label>
                <input value="{{ $article->prix_achat_unitaire ?? old('prix_achat_unitaire') }}" type="number" name="prix_achat_unitaire" id="prix_achat_unitaire" placeholder="Veuillez saisir le prix" min="0" step="any">


                <label for="date_fabrication">Date de fabrication</label>
                <input value="{{ $article ? \Carbon\Carbon::parse($article->date_fabrication)->format('Y-m-d\TH:i') : old('date_fabrication') }}" type="datetime-local" name="date_fabrication" id="date_fabrication">


                <label for="date_expiration">Date d'expiration</label>
                <input value="{{ $article ? \Carbon\Carbon::parse($article->date_expiration)->format('Y-m-d\TH:i') : old('date_expiration') }}" type="datetime-local" name="date_expiration" id="date_expiration">


                <button type="submit">Valider</button>
                
                {{-- Display success/error messages --}}
                @if(session('success'))
                    <div class="alert success">
                        {{ session('success') }}
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert error">
                        {{ session('error') }}
                    </div>
                @endif
                
                {{-- Display validation errors --}}
                @if ($errors->any())
                    <div class="alert error">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

            </form>
        </div>
        <div style="display: block;" class="box">
            <form action="{{ route('article') }}" method="get">
                <table class="mtable">
                    <tr>
                        <th>Nom article</th>
                        <th>Catégorie</th>
                        <th>Quantité</th>
                        <th>Prix de vente unitaire</th>
                        <th>Prix d'achat unitaire</th>
                        <th>Date fabrication</th>
                        <th>Date expiration</th>
                    </tr>
                    <tr>
                        <td>
                            <input type="text" name="nom_article" id="nom_article" placeholder="Veuillez saisir le nom" value="{{ request('nom_article') }}">
                        </td>
                        <td>
                            <select name="id_categorie" id="id_categorie" class="tom-select">
                                <option value="" disabled selected>---Choisir---</option>
                                @foreach ($categories as $categorie)
                                    <option value="{{ $categorie->id }}" {{ request('id_categorie') == $categorie->id ? 'selected': '' }}>
                                        {{ $categorie->libelle_categorie }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                         <td>
                            <input type="number" name="quantite" value="{{ request('quantite') }}" 
                                placeholder="Quantité" min="0">
                        </td>
                        <td>
                            <input type="number" name="prix_vente_unitaire" value="{{ request('prix_vente_unitaire') }}" 
                                placeholder="Prix de vente" min="0" step="any">
                        </td>
                        <td>
                            <input type="number" name="prix_achat_unitaire" value="{{ request('prix_achat_unitaire') }}" 
                                placeholder="Prix d'achat" min="0" step="any">
                        </td>
                        <td>
                            <input type="date" name="date_fabrication" value="{{ request('date_fabrication') }}">
                        </td>
                        <td>
                            <input type="date" name="date_expiration" value="{{ request('date_expiration') }}">
                        </td>
                    </tr>
                </table>

                <br>

                <button type="submit">Recherche</button>
            </form>

            <br>

            <table class="mtable">
                <tr>
                    <th class="sortable" data-sort="nom">Nom article<span class="sort-icon"></span></th>
                    <th class="sortable" data-sort="cat">Catégorie<span class="sort-icon"></span></th>
                    <th class="sortable" data-sort="num">Quantité<span class="sort-icon"></span></th>
                    <th class="sortable" data-sort="num">Prix de vente unitaire<span class="sort-icon"></span></th>
                    <th class="sortable" data-sort="num">Prix d'achat unitaire<span class="sort-icon"></span></th>
                    <th class="sortable" data-sort="date">Date fabrication<span class="sort-icon"></span></th>
                    <th class="sortable" data-sort="date">Date expiration<span class="sort-icon"></span></th>
                    <th>Action</th>
                </tr>
                @forelse($articles as $articleItem)
                    <tr class="{{ $articleItem->quantite < 5 ? 'low-stock-row' : '' }}">
                        <td>{{ $articleItem->nom_article }}</td>
                        <td>{{ $articleItem->categorie->libelle_categorie ?? 'N/A' }}</td>
                        <td>{{ $articleItem->quantite }}</td>
                        <td>{{ number_format($articleItem->prix_vente_unitaire, 2) }}</td>
                        <td>{{ number_format($articleItem->prix_achat_unitaire, 2) }}</td>
                        <td>{{ \Carbon\Carbon::parse($articleItem->date_fabrication)->format('d/m/Y H:i:s') }}</td>
                        <td>{{ \Carbon\Carbon::parse($articleItem->date_expiration)->format('d/m/Y H:i:s') }}</td>
                        <td>
                            <a href="{{ route('article', ['id' => $articleItem->id]) }}" 
                            title="Modifier" style="color: blue !important;">
                                <i class='bx bx-edit-alt'></i>
                            </a>
                            <a onclick="supprimeArticle({{ $articleItem->id }})" 
                            title="Supprimer" style="color: red; cursor: pointer;">
                                <i class='bx bx-x-circle'></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center;">Aucun article trouvé</td>
                    </tr>
                @endforelse
            </table>
        </div>
    </div>


@endsection

@section('scripts')

<script>
    function supprimeArticle(idArticle) {

    if (confirm("Voulez-vous vraiment supprimer ce article?")) {
        window.location.href = `{{ route('article.delete') }}?idArticle=${idArticle}`;
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
                if (sortKey === 'num') {
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