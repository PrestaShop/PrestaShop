# Spike — Admin API Tax Rules (#41677)

> **Issue :** https://github.com/PrestaShop/PrestaShop/issues/41677
> **PR Core :** https://github.com/PrestaShop/PrestaShop/pull/41703
> **PR ps_apiresources :** https://github.com/PrestaShop/ps_apiresources/pull/242

---

## 1. Contexte

Le correctif (nouvel endpoint `GET /tax-rules`) doit être intégré dans la version **9.2** de PrestaShop (branche `develop`). Or `ps_apiresources` est un module distribué **indépendamment** du Core et réutilisé sur les versions antérieures (9.1.x, 9.0.x…). Ce spike évalue si le fait de merger la PR `ps_apiresources` sans backport côté Core peut rendre instable la suite de tests — ou pire, casser l'endpoint en production — sur ces versions antérieures.

---

## 2. Fonctionnement actuel (avant les 2 PR)

**Côté Core**, le listing des tax rules existe déjà, mais uniquement pour l'écran BO d'édition d'un `TaxRulesGroup`, via `TaxRuleQueryBuilder` (`src/Core/Grid/Query/TaxRuleQueryBuilder.php`). Sur `9.1.x` (et sur `develop` avant la PR #41703), cette query builder :

- ne sélectionne que `id_tax_rule`, `description`, `country_name`, `state_name`, `zipcode`, `behavior`, `rate` — pas de `id_tax_rules_group`, `country_id`, `state_id`, `tax_name`, et pas de join sur `tax_lang` ;
- **exige** un filtre `taxRulesGroupId` en dur : `setParameter('idTaxRulesGroup', $filters['taxRulesGroupId'])`, sans aucun garde — impossible de lister toutes les règles tous groupes confondus.

**Côté AdminAPI**, il n'existe aujourd'hui **aucun endpoint** `/tax-rules` (seul `/tax-rules-groups` existe). Le mécanisme générique pour un `PaginatedList` adossé à une Grid (`QueryListProvider::paginationByGridDataFactory`) fonctionne ainsi :

1. Résoudre le service `gridDataFactory` dans le container (avec un `container->has()` — s'il manque, ça remonte proprement en 400, pas en crash).
2. Récupérer les lignes brutes (tableaux associatifs indexés par les alias SQL de la query builder).
3. Les « denormaliser » (Symfony Serializer) directement dans la classe DTO de la ressource API, via la table `ApiResourceMapping` qui traduit les clés SQL en noms de propriétés PHP.

Point important : cette denormalisation **ne remplit que les clés présentes** dans le tableau. Une propriété typée non-nullable sans valeur par défaut (`public int $countryId;`) qui ne reçoit jamais sa clé reste *uninitialized* — et la première tentative de la lire (typiquement au moment de sérialiser la réponse JSON) lève une `Error: Typed property ... must not be accessed before initialization`, donc une **500**, pas un 400 propre.

---

## 3. Ce que changent les 2 PR

- **#41703 (Core, `develop` uniquement)** ajoute les colonnes `country_id`, `state_id`, `tax_name` (+ join `tax_lang`) et rend le filtre `taxRulesGroupId` **optionnel**.
- **#242 (ps_apiresources)** ajoute `TaxRuleList` avec des propriétés typées non-nullable `taxRulesGroupId`, `countryId`, `stateId` qui ne peuvent être remplies que par ces nouvelles colonnes.

---

## 4. Impact concret sur les versions antérieures

`ps_apiresources` a **une seule branche `dev`**, testée par sa propre CI contre trois checkouts Core différents (`.github/workflows/integration.yml` → `prestashop_version: ['develop', '9.0.3', '9.1.x']`). C'est bien le même code de module qui tourne contre les 3 versions — la crainte initiale est donc structurellement fondée.

Preuve concrète relevée dans les logs CI actuels de la PR #242 (tous les jobs échouent aujourd'hui car #41703 n'est pas encore mergée dans `develop` upstream non plus) — job `(8.1, 9.1.x)` :

```
Failed asserting that 0 is equal to 3 or is greater than 3.
Tests: 489, Failures: 1, Warnings: 1, Skipped: 15.
```

C'est exactement l'effet du filtre obligatoire sur l'ancien Core : sans `taxRulesGroupId` fourni, `$filters['taxRulesGroupId']` est *undefined* → warning PHP + `WHERE id_tax_rules_group = NULL` → 0 lignes au lieu de ≥ 3. Le test plante dès la première assertion, ce qui **fait sauter en cascade** (via `@depends`) les 15 tests suivants — dont celui qui vérifie les valeurs de champs (`countryId`, `stateId`, etc.).

Il y a en réalité **deux problèmes empilés**, et la CI actuelle n'en révèle qu'un :

1. **Visible aujourd'hui** : le listing global (sans filtre de groupe) revient vide sur l'ancien Core → régression silencieuse.
2. **Latent, pas encore atteint par les tests actuels** : dès qu'une ligne est effectivement retournée (cas filtré par groupe), les colonnes `id_tax_rules_group` / `country_id` / `state_id` manquantes dans le grid row provoqueront un crash typé (`Error: ... must not be accessed before initialization`) → 500 au lieu d'une réponse propre. Corriger le point 1 sans toucher au point 2 ferait juste apparaître le point 2 immédiatement après.

---

## 5. Précédent existant dans le code

`ps_apiresources` a déjà rencontré ce type d'écart entre versions : `Discount*EndpointTest.php` utilise des gardes `class_exists(AddDiscountCommand::class)` pour sauter certaines assertions quand le Core testé n'a pas encore le domaine `Discount` (lui aussi un ajout `develop`-only).

Ce précédent ne s'applique pas tel quel ici : le service `TaxRuleQueryBuilder` **existe** déjà sur toutes les versions ce n'est pas une histoire de classe absente, c'est un jeu de colonnes différent. Un simple `class_exists` ne détecterait pas ça.

---

## 6. Périmètre de l'impact : liste uniquement

Le problème est confiné à l'endpoint de liste, il ne se propage pas au reste du domaine `TaxRule` :

- **Aucun endpoint item seul n'existe.** La PR #242 n'ajoute que `TaxRuleList.php` (`PaginatedList`) + son test d'intégration. Pas de `TaxRule.php`, pas de `GET /tax-rules/{id}`.
- **`TaxRuleQueryBuilder` n'est utilisé que par le Grid.** Vérifié par grep sur tout `src/` et `tests/` : aucune autre classe ne s'en sert. Les colonnes ajoutées par #41703 (`country_id`, `state_id`, `id_tax_rules_group`, `tax_name`) n'impactent donc que ce chemin.
- **Un second chemin, indépendant et déjà version-safe, existe pour l'item seul** : la query CQRS `GetTaxRuleForEditing` → `EditableTaxRule` (`src/Core/Domain/TaxRulesGroup/TaxRule/QueryResult/EditableTaxRule.php`), utilisée par le formulaire d'édition BO. Diff `9.1.x` vs branche courante sur ce fichier et son handler : **aucun changement**. Il expose déjà `taxRulesGroupId`, `countryId`, `stateId`, `taxId` en dur, sur toutes les versions supportées.

Conséquence pratique : si un `GET /tax-rules/{id}` est ajouté plus tard en s'appuyant sur `GetTaxRuleForEditing` (le pattern déjà suivi ailleurs — Grid pour la liste, CQRS Query pour l'item), il n'hérite pas de ce problème. Le risque ne réapparaîtrait que si quelqu'un réutilisait par erreur le Grid pour l'item seul.

---

## 7. Pistes de correction envisagées

**Option 1 — Backport du changement Core sur `9.1.x`.** Écartée : `9.1.x` n'accepte que des correctifs de bug, pas ce genre de changement porté par une feature `develop`, et `9.0.3` est un tag figé dans la matrice CI du module — un backport ne le corrigerait de toute façon pas.

**Option 2 — Tolérance défensive côté `ps_apiresources`.** ✅ Retenue et appliquée. Grâce au point 6, le correctif reste localisé à `TaxRuleList` : `taxRulesGroupId`, `countryId` et `stateId` passent en `?int = null`. Sur l'ancien Core, les clés `id_tax_rules_group`/`country_id`/`state_id` sont absentes du row du Grid → ces propriétés restent `null` au lieu de rester *uninitialized* → plus de crash à la sérialisation, réponse renvoyée avec ces champs à `null`. Sur 9.2 les colonnes existent, donc les champs sont toujours remplis normalement. Le listing global sans filtre reste vide sur l'ancien Core (comportement Core inchangé, déjà silencieux) — non traité ici, hors périmètre du fix module.

*Test adapté en conséquence* : `tests/Integration/ApiPlatform/TaxRuleEndpointTest.php` détecte la capacité (`taxRulesGroupId !== null` sur l'appel filtré par groupe, qui fonctionne identiquement sur toutes les versions) et branche ses assertions dessus :
- valeurs attendues de `taxRulesGroupId`/`countryId`/`stateId`/`taxName` conditionnées à ce flag ;
- listing global sans filtre : `0` attendu sur l'ancien Core (comportement Core inchangé, non corrigé) au lieu de planter l'assertion ;
- filtres `countryName`/`countryId` (clés de filtre grid elles aussi nouvelles en 9.2) : sautés sur l'ancien Core, où ils seraient silencieusement ignorés et renverraient tout le groupe au lieu du sous-ensemble attendu.

Pas de changement nécessaire sur `testListTaxRulesPagination`, `testSortTaxRules`, `testListTaxRuleWithoutTax` : ils ne dépendent que de colonnes déjà présentes avant #41703.

*Vérifié en passant* : le service `prestashop.core.grid.data.factory.tax_rule` utilisé par `TaxRuleList` est le `DoctrineGridDataFactory` brut, pas le décorateur `TaxRuleGridDataFactory` qui traduit `behavior` en libellé et formate `rate` en pourcentage pour le BO — donc pas de risque de type mismatch caché sur ces deux champs.

**Option 3 — Ne pas merger #242 sur `dev` tout de suite.** Écartée : dépendait de l'option 1 pour avoir un état de sortie propre.

---
---

# English message — for GitHub

Checked whether merging #242 before #41703 reaches older core would cause real breakage. It would: on 9.1.x/9.0.x, `TaxRuleQueryBuilder` doesn't return `country_id`/`state_id`/`id_tax_rules_group`/`tax_name`, so `TaxRuleList`'s non-nullable properties stay uninitialized and crash on serialization instead of failing cleanly.

Good news: it's scoped to the list endpoint only. No single-item endpoint exists yet, and the query that would back one (`GetTaxRuleForEditing`) already has these fields on every version.

Backporting to 9.1.x isn't realistic here, so we'll make `TaxRuleList` tolerant of the missing columns instead. PR coming.
