<!-- Scripts communs -->
<script>
    const API_BASE = '/api';
</script>

<?php 
// Récupère les segments de l'URL
$segment1 = service('uri')->getSegment(1); // ex: 'dlc', 'clients'
$segment2 = service('uri')->getSegment(2); // ex: 'catalogue', 'details'

// Chargement conditionnel des scripts
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
<?php elseif ($segment1 === 'mouvements') : ?>
    <script src="/assets/js/ecoulement-mouvements.js"></script>
<?php elseif ($segment1 === 'etat-stock') : ?>
    <script src="/assets/js/ecoulement-stock.js"></script>
<?php endif; ?>
</body>
</html>