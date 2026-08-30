<!-- Scripts communs -->
<script>
    const API_BASE = '/api';
</script>

<!-- Sidebar Toggle -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('sidebar-toggle');
        const brandText = document.getElementById('brand-text');
        
        if (toggleBtn) {
            toggleBtn.addEventListener('click', function() {
                // Toggle de la largeur
                const isCollapsed = sidebar.classList.toggle('collapsed');
                
                if (isCollapsed) {
                    sidebar.style.width = '80px';
                    toggleBtn.querySelector('span').textContent = 'chevron_right';
                    // Cacher le texte
                    if (brandText) brandText.style.opacity = '0';
                } else {
                    sidebar.style.width = '280px';
                    toggleBtn.querySelector('span').textContent = 'chevron_left';
                    if (brandText) brandText.style.opacity = '1';
                }
            });
        }
    });
</script>

<?php 
$segment1 = service('uri')->getSegment(1);
$segment2 = service('uri')->getSegment(2);

if ($segment1 === 'dlc' && $segment2 === 'catalogue') : ?>
    <script src="/assets/js/catalogue.js"></script>
<?php elseif ($segment1 === 'dlc' && $segment2 === 'calculateur') : ?>
    <script src="/assets/js/calculateur.js"></script>
<?php elseif ($segment1 === 'produits-ecoulement') : ?>
    <script src="/assets/js/ecoulement-produits.js"></script>
<?php elseif ($segment1 === 'clients' && $segment2 === 'details') : ?>
    <script src="/assets/js/ecoulement-client-details.js"></script>
<?php elseif ($segment1 === 'clients' && empty($segment2)) : ?>
    <script src="/assets/js/ecoulement-clients.js"></script>
<?php elseif ($segment1 === 'ventes') : ?>
    <script src="/assets/js/ecoulement-ventes.js"></script>
<?php elseif ($segment1 === 'mouvements') : ?>
    <script src="/assets/js/ecoulement-mouvements.js"></script>
<?php elseif ($segment1 === 'etat-stock') : ?>
    <script src="/assets/js/ecoulement-stock.js"></script>
<?php elseif ($segment1 === 'recapitulation') : ?>
    <script src="/assets/js/ecoulement-recap.js"></script>
<?php elseif ($segment1 === 'stock-gestion') : ?>
    <script src="/assets/js/ecoulement-stock-gestion.js"></script>
<?php endif; ?>
</body>
</html>