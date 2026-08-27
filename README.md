# milkyway-ERP
As part of my internship at the dairy company Milky Way, I am developing an ERP system to improve its management and to truly apply my computer skills.

# how to launch 
php spark serve
(par défaut http://localhost:8080)

Voici les 5 prompts pour Stitch, cohérents avec le style déjà établi (sidebar "Milky Way", palette bleu/vert, Material Symbols).

---

Chaque colonne d'en-tête du tableau est cliquable pour trier (icône flèche 
haut/bas), avec bascule ascendant/descendant. Les colonnes textuelles 
trient par ordre alphabétique, les colonnes de date/nombre trient par 
ordre chronologique/numérique.

**Prompt 0 — Sidebar avec drilldown (à inclure/rappeler dans chaque écran)**

```
La sidebar gauche existante doit être modifiée : sous l'item "Écoulement", 
ajoute une sous-navigation dépliable (drilldown) avec ces 5 liens, en retrait 
et police légèrement plus petite : Clients, Produits, Entrées/Sorties, 
État des stocks, Récapitulation. Le lien actif (correspondant à l'écran 
affiché) est surligné avec le fond blanc arrondi (comme "Gestion DLC" 
actuellement), et le parent "Écoulement" reste visuellement marqué comme 
section active tant qu'on est sur un de ses sous-écrans.
```

---

**Prompt 1 — Clients (liste)**

```
Écran "Clients" du module Écoulement, sidebar Écoulement > Clients active.
Tableau listant les clients : Nom, Contact (téléphone), Adresse, et une 
colonne "Total acheté" (montant). Bouton "+ Ajouter un client" en haut à 
droite. Chaque ligne cliquable mène à une page détail. Icônes Modifier/
Supprimer par ligne. Modal d'ajout/modification avec champs Nom, Contact, 
Adresse.
et un filtre par periode (semaine et mois).
Trie 
```

---

**Prompt 2 — Détails client**

```
Écran "Détails client" (drill-down depuis Clients), sidebar Écoulement > 
Clients toujours active. En-tête avec nom du client, contact, adresse, et 
un badge "Total acheté : XXX Ar". Sous l'en-tête, un filtre par période 
(date début/fin, ou raccourcis "Cette semaine / Ce mois / Tout"). En 
dessous, un tableau historique des achats de ce client filtré par cette 
période : Date, Produit, Quantité, Prix unitaire, Total — colonnes 
triables (alphabétique/chronologique, ascendant/descendant). Bouton 
retour vers la liste clients.
```

---

**Prompt 3 — Produits**

```
Écran "Produits" du module Écoulement, sidebar Écoulement > Produits active.
Tableau : Nom du produit, Durée de conservation (jours), Prix de vente. 
Bouton "+ Ajouter un produit". Icônes Modifier/Supprimer par ligne. 
Modal d'ajout/modification avec champs Nom, Durée de conservation, Prix 
de vente.
```

---

**Prompt 4 — Entrées/Sorties**

```
Écran "Entrées/Sorties" du module Écoulement, sidebar Écoulement > 
Entrées/Sorties active. En haut, une barre de filtres : Période (date 
début/fin), Type (Entrée/Sortie/Tous), Cause (Vente/Non-conforme, actif 
si Sortie), Produit (liste déroulante), Client (actif si cause=Vente). 
Bouton "+ Nouveau mouvement". Un diagramme en bâtons montrant les 
quantités entrées vs sorties, sous les filtres. En dessous, tableau des 
mouvements : Date, Type, Produit, Quantité, Cause, Client. Modal d'ajout 
avec les mêmes champs, formulaire qui change dynamiquement selon le type 
sélectionné (Entrée = pas de cause/client ; Sortie = cause obligatoire, 
client si Vente).
```

---

**Prompt 5 — État des stocks**

```
Écran "État des stocks" du module Écoulement, sidebar Écoulement > État 
des stocks active. Filtres en haut : recherche par nom de produit, et un 
champ "Seuil bas" (nombre) pour surligner les produits en dessous. Un 
diagramme en camembert montrant la répartition du stock actuel par 
produit. En dessous, tableau : Produit, Quantité en stock, avec une 
pastille rouge si en dessous du seuil bas défini.
```

---

**Prompt 6 — Récapitulation**

```
Écran "Récapitulation" du module Écoulement, sidebar Écoulement > 
Récapitulation active. Un toggle "Semaine / Mois" en haut à droite. Une 
courbe d'évolution des ventes (montant total) dans le temps, selon la 
période choisie. En dessous, deux tableaux côte à côte : "Par client" 
(Client, Nombre de produits livrés, Montant total) et "Par produit" 
(Produit, Quantité vendue, Quantité perdue/non-conforme, Valeur vendue).
```