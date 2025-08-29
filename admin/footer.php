    </main>
  </div>
</div>

<!-- Bootstrap JS bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
  document.getElementById('sidebarToggle')?.addEventListener('click', function () {
    document.getElementById('sidebarMenu').classList.toggle('show');
  });

  // Prevent orders dropdown from closing when clicking on sub-menu items
  document.addEventListener('DOMContentLoaded', function() {
    const ordersDropdown = document.getElementById('ordersCollapse');
    const subMenuLinks = ordersDropdown.querySelectorAll('.nav-link');
    
    subMenuLinks.forEach(function(link) {
      link.addEventListener('click', function(e) {
        // Prevent the default collapse behavior
        e.stopPropagation();
        
        // Store the target URL
        const targetUrl = link.href;
        
        // Navigate immediately without closing dropdown
        window.location.href = targetUrl;
      });
    });
    
    // Keep dropdown open when clicking inside it
    ordersDropdown.addEventListener('click', function(e) {
      e.stopPropagation();
    });
  });
</script>

</body>
</html>
