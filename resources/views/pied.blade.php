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
  </body>
</html>