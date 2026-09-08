Petite précision d'abord, pour éviter la confusion : **l'UUID n'est pas lié à Supabase (Étape 6)** — c'est une colonne dans TES tables MySQL (mouvements de vente/stock), utile dès maintenant pour l'endpoint `push`. Étape 6 sera juste la config du service Supabase (relais), sans rapport avec le schéma de tes tables. Donc oui, il faut le faire maintenant, à l'Étape 5.

## Mini to-do — Étape 5

### OK 1. Ajoute la colonne UUID dans tes tables de mouvements
Ajouter une colonne UUID a Mouvement 

generer un UUID pour chaque mouvement

### OK 2. Ajoute `updated_at` sur les tables lues au pull (produits, clients)
Si pas déjà présent :
```php
$this->forge->addColumn('produits', ['updated_at' => ['type' => 'DATETIME', 'null' => true]]);
$this->forge->addColumn('clients', ['updated_at' => ['type' => 'DATETIME', 'null' => true]]);
```
Vérifie que ton `Model` CI4 a bien `protected $useTimestamps = true;` pour que ce champ se mette à jour automatiquement.

### 3. Crée le filtre (middleware) JWT — `app/Filters/JwtAuthFilter.php`
```php
<?php
namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $authHeader = $request->getHeaderLine('Authorization');
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return service('response')->setStatusCode(401)->setJSON(['error' => 'Token manquant']);
        }

        $token = substr($authHeader, 7);

        try {
            $decoded = JWT::decode($token, new Key(getenv('JWT_SECRET_KEY'), 'HS256'));
        } catch (\Exception $e) {
            return service('response')->setStatusCode(401)->setJSON(['error' => 'Token invalide ou expiré']);
        }

        // Vérification du rôle si un rôle requis est passé en argument
        if ($arguments && !in_array($decoded->role, $arguments)) {
            return service('response')->setStatusCode(403)->setJSON(['error' => 'Rôle non autorisé']);
        }

        $request->user_id = $decoded->user_id;
        $request->role = $decoded->role;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
```

### OK 4. Enregistre le filtre — `app/Config/Filters.php`
```php
public array $aliases = [
    // ...
    'jwtAuth' => \App\Filters\JwtAuthFilter::class,
];
```

### OK 5. Applique le filtre sur les routes — `app/Config/Routes.php`
```php
$routes->group('api/sync', ['filter' => 'jwtAuth:vente,stocks,admin'], function($routes) {
    $routes->post('push', 'Api\SyncController::push');
    $routes->get('pull', 'Api\SyncController::pull');
});
```

### OK 6. Crée `app/Controllers/Api/SyncController.php`

```php
<?php
namespace App\Controllers\Api;

use App\Controllers\BaseController;

class SyncController extends BaseController
{
    public function push()
    {
        $mouvements = $this->request->getJSON(true)['mouvements'] ?? [];
        $mouvementModel = new \App\Models\MouvementModel();
        $produitModel = new \App\Models\ProduitModel();

        $resultats = [];

        foreach ($mouvements as $m) {
            // Anti-doublon : UUID déjà connu ?
            if ($mouvementModel->where('uuid', $m['uuid'])->first()) {
                $resultats[] = ['uuid' => $m['uuid'], 'statut' => 'deja_synchronise'];
                continue;
            }

            // Validation métier
            $produit = $produitModel->where('id', $m['product_id'])
                                     ->where('is_deleted', 0)
                                     ->first();
            if (!$produit) {
                log_message('error', "Produit invalide pour mouvement {$m['uuid']}: product_id {$m['product_id']}");
                $resultats[] = ['uuid' => $m['uuid'], 'statut' => 'rejete', 'raison' => 'produit_invalide'];
                continue;
            }

            $mouvementModel->insert([
                'uuid'       => $m['uuid'],
                'product_id' => $m['product_id'],
                'quantite'   => $m['quantite'],
                'type'       => $m['type'],
                'user_id'    => $this->request->user_id,
            ]);
            $resultats[] = ['uuid' => $m['uuid'], 'statut' => 'ok'];
        }

        return $this->response->setJSON(['resultats' => $resultats]);
    }

    public function pull()
    {
        $since = $this->request->getGet('since') ?? '1970-01-01 00:00:00';

        $produitModel = new \App\Models\ProduitModel();
        $clientModel  = new \App\Models\ClientModel();

        return $this->response->setJSON([
            'produits'    => $produitModel->where('updated_at >=', $since)->findAll(),
            'clients'     => $clientModel->where('updated_at >=', $since)->findAll(),
            'server_time' => date('Y-m-d H:i:s'),
        ]);
    }
}
```

### OK 7. Teste avec curl (token de l'étape 4)
```bash
TOKEN="colle_ton_jwt_ici"

curl -k -X GET "https://localhost:8443/api/sync/pull?since=1970-01-01" \
  -H "Authorization: Bearer $TOKEN"

curl -k -X POST https://localhost:8443/api/sync/push \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"mouvements":[{"uuid":"test-uuid-1","product_id":1,"quantite":5,"type":"vente"}]}'
```

Une fois ces deux endpoints qui répondent correctement, tu passes à l'Étape 6 (Supabase).

TOKEN=$(curl -k -X POST https://localhost:8443/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"login":"Andry","mot_de_passe":"admin123"}' \
  | grep -o '"token":"[^"]*"' | cut -d'"' -f4)

echo $TOKEN;

# Pull
curl -k -X GET "https://localhost:8443/api/sync/pull?since=1970-01-01" \
  -H "Authorization: Bearer $TOKEN"

# Push (adapte les données)
curl -k -X POST https://localhost:8443/api/sync/push \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"mouvements":[{"uuid":"test-uuid-1","produit_id":1,"quantite":5,"type":"sortie","cause":"vente"}]}'