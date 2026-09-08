## Mini to-do — Étape 6 (Supabase = boîte aux lettres)

### OK 1. Crée le projet
- Va sur supabase.com → New project → choisis une région proche (Europe si rien en Afrique) → note l'URL du projet et le mot de passe DB généré.

### OK 2. Crée la table relais (SQL Editor)
```sql
create table public.sync_relais (
  id bigint generated always as identity primary key,
  uuid_mouvement uuid not null unique,
  role_expediteur text not null,
  payload jsonb not null,
  device_id text,
  created_at timestamptz not null default now(),
  statut text not null default 'pending'
);

create index idx_sync_relais_statut on public.sync_relais (statut);
create index idx_sync_relais_created_at on public.sync_relais (created_at);
```

### OK 3. Active RLS + retire les droits par défaut
```sql
alter table public.sync_relais enable row level security;
revoke all on public.sync_relais from anon, authenticated;
```
**Important** (changement récent chez Supabase) : activer RLS ne suffit plus seul, il faut aussi accorder explicitement les droits SQL de base avant que les policies fonctionnent — d'où le `revoke` ci-dessus suivi du `grant` ciblé à l'étape 4.

### OK 4. Autorise l'INSERT uniquement, pour le rôle `anon`
```sql
grant insert on public.sync_relais to anon;

create policy "mobile_insert_only"
on public.sync_relais
for insert
to anon
with check (statut = 'pending');
```
Le `with check` empêche même d'insérer une ligne avec un `statut` autre que `pending` — le mobile ne peut pas se faire passer pour un enregistrement déjà "traité".

### OK 5. Vérifie qu'aucune policy SELECT/UPDATE/DELETE n'existe pour `anon`
Confirme dans le SQL Editor :
```sql
select * from pg_policies where tablename = 'sync_relais';
```
Tu dois voir **une seule ligne** (`mobile_insert_only`, opération INSERT). Si tu vois autre chose (ex: policy générée par erreur via l'interface graphique), supprime-la.

### OK 6. Récupère les 2 clés (Settings → API)
- **`anon` key** → à mettre dans l'app Flutter uniquement
- **`service_role` key** → à mettre **uniquement** dans le `.env` du PC (jamais versionnée, jamais dans le mobile)

### OK 7. Teste que la clé `anon` est bien restreinte
```bash
# Ceci DOIT marcher (insert)
ANON_KEY="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Imxnenh5d210bW5ycHNwaWFtdXRpIiwicm9sZSI6ImFub24iLCJpYXQiOjE3ODg4OTM4MjgsImV4cCI6MjEwNDQ2OTgyOH0.kGwvpVI4YID2AcMaQWgew50EOE9Ba7AKrtmpB4DSncg"

curl -X POST 'https://lgzxywmtmnrpspiamuti.supabase.co/rest/v1/sync_relais' \
  -H "apikey: $ANON_KEY" \
  -H "Authorization: Bearer $ANON_KEY" \
  -H "Content-Type: application/json" \
  -d '{"uuid_mouvement":"11111111-1111-1111-1111-111111111111","role_expediteur":"vente","payload":{"test":true}}'

# Ceci DOIT échouer (403/permission denied)
ANON_KEY="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Imxnenh5d210bW5ycHNwaWFtdXRpIiwicm9sZSI6ImFub24iLCJpYXQiOjE3ODg4OTM4MjgsImV4cCI6MjEwNDQ2OTgyOH0.kGwvpVI4YID2AcMaQWgew50EOE9Ba7AKrtmpB4DSncg"

curl 'https://lgzxywmtmnrpspiamuti.supabase.co/rest/v1/sync_relais?select=*' \
  -H "apikey: $ANON_KEY" \
  -H "Authorization: Bearer $ANON_KEY"
```
Si le deuxième test réussit (tu vois des données), la policy est mal configurée — reviens à l'étape 4/5.

### OK 8. Stocke la `service_role` key côté PC
Ajoute dans le `.env` du container CI4 :
```
SUPABASE_URL=https://ton_projet.supabase.co
SUPABASE_SERVICE_ROLE_KEY=xxxxx
```
(utilisée plus tard à l'Étape 8, pour que le PC aille lire la file d'attente)

Une fois le test #7 validé (insert OK, select refusé), l'Étape 6 est terminée — tu peux passer à l'Étape 7 (app mobile Flutter).