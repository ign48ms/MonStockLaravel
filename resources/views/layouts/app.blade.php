<?php
  include_once("../app/Legacy/function.php");
?>

<!DOCTYPE html>
<html lang="fr" dir="ltr">
  <head>
    <meta charset="UTF-8" />
    <title>@yield('title', 'MonStock')</title>
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}"/>
    <!-- Boxicons CDN Link -->
    <link
      href="https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css"
      rel="stylesheet"
    />

    <!-- Tom Select CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">



    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  </head>
  <body>
    <div class="sidebar hidden-print">
      <div class="logo-details">
      <i class='bx bx-cart-alt'></i>
        <span class="logo_name">MonStock</span>
      </div>
      <ul class="nav-links">
        <li>
          <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bx bx-grid-alt"></i>
            <span class="links_name">Dashboard</span>
          </a>
        </li>
        <li>
          <a href="{{ route('article') }}" class="{{ request()->routeIs('article') ? 'active' : '' }}">
            <i class="bx bx-box"></i>
            <span class="links_name">Article</span>
          </a>
        </li>
        <li>
          <a href="{{ route('vente') }}" class="{{ request()->routeIs('vente') ? 'active' : '' }}">
          <i class="bx bx-shopping-bag"></i>
            <span class="links_name">Vente</span>
          </a>
        </li>
        <li>
          <a href="{{ route('achat') }}" class="{{ request()->routeIs('achat') ? 'active' : '' }}">
            <i class="bx bx-list-ul"></i>
            <span class="links_name">Achat</span>
          </a>
        </li>
        <li>
          <a href="{{ route('client') }}" class="{{ request()->routeIs('client') ? 'active' : '' }}">
            <i class="bx bx-user"></i>
            <span class="links_name">Client</span>
          </a>
        </li>
        <li>
          <a href="{{ route('fournisseur') }}" class="{{ request()->routeIs('fournisseur') ? 'active' : '' }}">
            <i class="bx bx-user"></i>
            <span class="links_name">Fournisseur</span>
          </a>
        </li>
        <li>
          <a href="{{ route('categorie') }}" class="{{ request()->routeIs('categorie') ? 'active' : '' }}">
          <i class='bx bx-category-alt'></i>
            <span class="links_name">Catégorie</span>
          </a>
        </li>
      </ul>
    </div>
    <section class="home-section">
      <nav class="hidden-print">
        <div class="sidebar-button">
          <i class="bx bx-menu sidebarBtn"></i>
          <span class="dashboard">@yield('page-title', 'Dashboard')</span>
        </div>
      </nav>

       <!-- This is where your page content will be inserted -->

      <div class="home-content">
            @yield('content')
      </div>
    </section>

    <script>
      let sidebar = document.querySelector(".sidebar");
      let sidebarBtn = document.querySelector(".sidebarBtn");
      sidebarBtn.onclick = function () {
        sidebar.classList.toggle("active");
        if (sidebar.classList.contains("active")) {
          sidebarBtn.classList.replace("bx-menu", "bx-menu-alt-right");
        } else sidebarBtn.classList.replace("bx-menu-alt-right", "bx-menu");
      };
    </script>
    <!-- Tom Select JS -->
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

    <script>
      document.addEventListener("DOMContentLoaded", function () {
        const selects = document.querySelectorAll("select.tom-select");
        selects.forEach(select => {
          new TomSelect(select, {
            create: false,
            sortField: {
              field: "text",
              direction: "asc"
            },
              closeAfterSelect: true,  // Close dropdown after selection
              onItemAdd: function() {
                this.blur();  // Remove focus after item is selected
              }
          });
        });
      });
    </script>

    @yield('scripts')
  
  </body>
</html>