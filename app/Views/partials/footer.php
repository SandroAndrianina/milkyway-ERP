<!-- Scripts communs -->
<script>
    const API_BASE = '/api';
</script>

<?php 
// Récupère le nom de la vue (ex: 'catalogue' ou 'calculateur')
$view = service('uri')->getSegment(2); 
if ($view === 'catalogue') : ?>
    <script src="/assets/js/catalogue.js"></script>
<?php elseif ($view === 'calculateur') : ?>
    <script src="/assets/js/calculateur.js"></script>
<?php endif; ?>

</body>
</html>