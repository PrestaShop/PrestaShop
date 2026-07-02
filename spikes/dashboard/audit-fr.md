# Spike — Dashboard PrestaShop (#41522)

> **Issue :** https://github.com/PrestaShop/PrestaShop/issues/41522

---

## 1. Contexte

Le Dashboard admin est **100 % legacy** : aucune migration Symfony n'a été amorcée. Ce spike cartographie tout ce qui compose la page, identifie les points de blocage, et évalue la faisabilité d'une migration.

---

## 2. Contrôleur

| Fichier | Type |
|---------|------|
| `controllers/admin/AdminDashboardController.php` | Legacy (`AdminController`) |

**Points clés :**
- Routage legacy via `PrestaShopBundle\Controller\Admin\LegacyController::legacyPageAction`
- Actions AJAX dans le contrôleur :
  - `ajaxProcessRefreshDashboard` — données des widgets
  - `ajaxProcessSetSimulationMode` — mode démo
  - `ajaxProcessSaveDashConfig` — config widget
- Config : `PS_DASHBOARD_SIMULATION`, dates par employé (`stats_date_from`, `stats_date_to`, etc.)

---

## 3. Hooks dispatched

| Hook | Déclencheur | Paramètres |
|------|------------|------------|
| `dashboardZoneOne` | `renderView()` | `date_from`, `date_to` |
| `dashboardZoneTwo` | `renderView()` | `date_from`, `date_to` |
| `dashboardZoneThree` | `renderView()` | `date_from`, `date_to` (zone optionnelle) |
| `dashboardData` | `ajaxProcessRefreshDashboard()` | `date_from`, `date_to`, `compare_from`, `compare_to`, `extra` |
| `displayDashboardToolbarTopMenu` | `page_header_toolbar.tpl` | — |
| `displayDashboardTop` | `page_header_toolbar.tpl` | — |
| `displayDashboardToolbarIcons` | Déclaré mais **non utilisé** dans les templates | — |
| `actionAdminControllerSetMedia` | Utilisé par les modules pour injecter leurs assets | — |

---

## 4. Templates

| Fichier | Rôle |
|---------|------|
| `admin-dev/themes/default/template/controllers/dashboard/helpers/view/view.tpl` | Vue principale (Smarty, default theme) |
| `admin-dev/themes/default/template/helpers/calendar/calendar.tpl` | Widget calendrier (rendu par `HelperCalendar`) |
| `admin-dev/themes/default/template/page_header_toolbar.tpl` | Toolbar hooks |

La vue principale est dans le **default theme**, pas dans le new-theme. Layout 3 colonnes Bootstrap : Zone One `col-lg-3`, Zone Two `col-lg-7/9`, Zone Three `col-lg-2` (vide nativement).

---

## 5. JavaScript

| Fichier | Nature |
|---------|--------|
| `js/admin/dashboard.js` | Core dashboard — AJAX, rendu des widgets, config |
| `js/vendor/d3.v3.min.js` | D3.js **v3** (obsolète, ~2016) |
| `admin-dev/themes/{theme}/js/vendor/nv.d3.min.js` | NVD3 — graphiques |
| `admin-dev/themes/{theme}/js/date-range-picker.js` | Calendrier (chargé par `HelperCalendar`) |
| `admin-dev/themes/{theme}/js/calendar.js` | Calendrier (chargé par `HelperCalendar`) |

Aucun entry point webpack — tout chargé via `addJS()` legacy. Pas d'asset Dashboard dans le new-theme.

Fonctions clés de `dashboard.js` :

| Fonction | Rôle |
|----------|------|
| `refreshDashboard(module_name, extra)` | Appel AJAX pour rafraîchir un widget |
| `setDashboardDateRange(action)` | Change la plage de dates et rafraîchit |
| `data_value()` | Rendu valeur simple |
| `data_trends()` | Rendu indicateurs tendance (up/down/right) |
| `data_table()` | Rendu tableau |
| `data_chart()` | Rendu graphique NVD3 |
| `data_list_small()` | Rendu liste compacte |
| `toggleDashConfig()` / `saveDashConfig()` | Gestion config widgets |

---

## 6. Modules natifs intégrés

| Module | Zone | Hooks | JS propre |
|--------|------|-------|-----------|
| `dashactivity` | ZoneOne | `dashboardZoneOne`, `dashboardData`, `actionAdminControllerSetMedia` | `dashactivity.js` (pie chart NVD3) |
| `dashtrends` | ZoneTwo | `dashboardZoneTwo`, `dashboardData`, `actionAdminControllerSetMedia` | `dashtrends.js` (line chart NVD3) |
| `dashgoals` | ZoneTwo | `dashboardZoneTwo`, `dashboardData`, `actionAdminControllerSetMedia` | `dashgoals.js` (bar chart NVD3) |
| `dashproducts` | ZoneTwo | `dashboardZoneTwo`, `dashboardData` | aucun |

Pattern commun :

```php
// Détection du contexte Dashboard pour charger les assets
public function hookActionAdminControllerSetMedia() {
    if (get_class($this->context->controller) == 'AdminDashboardController') {
        $this->context->controller->addJs($this->_path . 'views/js/dashXxx.js');
    }
}

// Rendu de zone : Smarty assign + HelperForm + display()
public function hookDashboardZoneOne($params) {
    $this->context->smarty->assign([
        'dashactivity_config_form' => $this->renderConfigForm(), // ← HelperForm ici
        'date_format' => $this->context->language->date_format_lite,
        'link' => $this->context->link,
    ]);
    return $this->display(__FILE__, 'dashboard_zone_one.tpl');
}

// Données AJAX
public function hookDashboardData($params) {
    return [
        'data_value'      => [...],
        'data_trends'     => [...],
        'data_list_small' => [...],
        'data_chart'      => [...],
    ];
}
```

Points notables :
- `dashgoals` utilise une table `ConfigurationKPI` spécifique (pas Doctrine)
- Tous les modules utilisent `Db::getInstance()` avec SQL brut
- Templates de hook en **Smarty**
- `dashactivity::hookDashboardZoneOne` appelle `renderConfigForm()` → `HelperForm` → Smarty

---

## 7. Analyse d'impact

| Point de blocage | Sévérité | Détail |
|-----------------|----------|--------|
| **Modules utilisent `HelperForm`/`HelperCalendar` dans leurs hooks** | Critique | Ces helpers rendent des templates Smarty (`helpers/form/form.tpl`, `helpers/calendar/calendar.tpl`) qui nécessitent que les répertoires Smarty admin soient initialisés — ce qu'`AdminController::init()` fait normalement |
| **Couplage `get_class($controller) == 'AdminDashboardController'`** | Haute | Les 4 modules vérifient le nom de classe exact pour charger leurs JS |
| **Templates de hook en Smarty** | Haute | `dashboard_zone_one.tpl`, `dashboard_zone_two.tpl` etc. sont des templates Smarty dans les modules |
| **`dashboard.js` non bundlé** | Haute | Fichier JS legacy sans module system — à porter ou à conserver tel quel |
| **D3 v3 + NVD3** | Moyenne | Obsolètes — remplacement = refacto dans chaque module |
| **AJAX pattern `hookDashboardData`** | Haute | Contrat implicite entre `dashboard.js` et les modules — à formaliser si migration JS |
| **SQL brut dans les modules** | Moyenne | `Db::getInstance()` dans tous les modules — pas un bloquant pour migrer le contrôleur |
| **Zone Three inutilisée** | Info | Aucun module natif ne l'utilise |
| **`displayDashboardToolbarIcons`** | Info | Déclaré, jamais appelé dans les templates |

---

## 8. Architecture actuelle

```
AdminDashboardController (legacy)
  ├── Charge assets : dashboard.js + D3v3 + NVD3 + calendar.js (via setMedia)
  ├── Rend : default/template/controllers/dashboard/helpers/view/view.tpl (Smarty)
  └── Hooks zone → modules (HTML Smarty) + dashboardData (JSON pour dashboard.js)

Modules dash* (assets via hookActionAdminControllerSetMedia)
  ├── hookDashboardZoneX → Smarty assign + HelperForm + display()
  └── hookDashboardData  → tableau PHP → dashboard.js (AJAX)
```

---

## 9. Enseignements du POC tenté

Un POC partiel a été tenté (Symfony controller + Twig template + shim `AdminDashboardController`). Deux blocages confirmés en pratique :

**Blocage 1 — `HelperCalendar` inutilisable dans le contexte Symfony**

`HelperCalendar::generate()` appelle `Helper::generate()` qui crée un template Smarty via `$context->smarty->createTemplate('helpers/calendar/calendar.tpl')`. Dans le contexte Symfony, Smarty n'a pas les répertoires admin dans ses paths.

**Résolution :** Porter `calendar.tpl` directement en Twig — le template est simple (100 lignes), la migration est mécanique. Les JS associés (`date-range-picker.js`, `calendar.js`) sont chargés dans `{% block extra_javascripts %}`.

**Blocage 2 — `HelperForm` dans les hooks des modules**

`dashactivity::hookDashboardZoneOne()` appelle `renderConfigForm()` → `new HelperForm()` → Smarty cherche `helpers/form/form.tpl`.

Trace constatée :
```
PrestaShop\PrestaShop\Core\Exception\CoreException:
Unable to load template 'file:helpers/form/form.tpl'
  at classes/Hook.php:469
  at HookCore::callHookOn(object(dashactivity), 'dashboardZoneOne', ...)
  at DashboardController->indexAction(object(Request))
```

**Résolution :** Ajouter le répertoire de templates du thème admin aux paths Smarty avant de dispatcher les hooks de zone :

```php
private function ensureAdminThemeTplDir(\Context $context): void
{
    $boTheme = (Validate::isLoadedObject($context->employee) && $context->employee->bo_theme)
        ? $context->employee->bo_theme
        : 'default';
    if (!file_exists(_PS_BO_ALL_THEMES_DIR_ . $boTheme . '/template')) {
        $boTheme = 'default';
    }
    $tplDir = _PS_BO_ALL_THEMES_DIR_ . $boTheme . '/template/';
    if (!in_array($tplDir, (array) $context->smarty->getTemplateDir())) {
        $context->smarty->addTemplateDir($tplDir);
    }
}
```

> **Finding structurel :** Ce setup sera nécessaire pour toute page Symfony migrée dont les modules de hook utilisent des `Helper*` legacy. C'est un pattern récurrent pour l'ensemble de l'effort de migration.

**Conclusion du POC** : la migration du contrôleur seul est techniquement faisable avec ces deux ajustements. La page peut s'afficher. En revanche, le rendu des zones reste entièrement en Smarty (côté modules) — la migration ne change pas l'expérience utilisateur, elle change seulement la couche contrôleur/routing.

---

## 10. Évaluation : faut-il démarrer un vrai POC ?

**Oui, les deux blocages sont résolubles sans toucher aux modules.**

| Critère | État |
|---------|------|
| Contrôleur Symfony fonctionnel | ✅ Faisable, patterns connus |
| Template Twig pour le layout | ✅ Migration mécanique de `view.tpl` |
| Calendrier en Twig | ✅ Migration mécanique de `calendar.tpl` (~100 lignes) |
| Compatibilité modules (shim + Smarty dirs) | ✅ ~2 lignes de setup |
| AJAX `dashboard.js` sans le modifier | ✅ Conserver les mêmes URLs et formats JSON |
| Graphiques fonctionnels | ✅ Assets D3/NVD3 chargés manuellement via `asset()` |
| **Effort estimé** | **~1 à 2 jours de travail propre** |

La migration n'apporte pas de valeur visible à l'utilisateur mais elle pose les bases pour migrer ensuite les modules `dash*` vers une architecture moderne.

---

## 11. Premières étapes de la migration

### Étape 1 — Symfony controller

Créer `src/PrestaShopBundle/Controller/Admin/DashboardController.php` étendant `PrestaShopAdminController`.

Actions à porter :
- `indexAction(Request $request)` : page principale + dispatch hooks zone
- AJAX inline (via `?ajax=1&action=X`) : `refreshDashboard`, `setSimulationMode`, `saveDashConfig`
- `submitDateRange` : sauvegarde de la plage de dates employé

Setup obligatoire dans `indexAction` **avant** de dispatcher les hooks :

```php
// 1. Shim pour la compat get_class() des modules
$shim = new AdminDashboardController();
Context::getContext()->controller = $shim;

// 2. Déclencher l'injection des assets modules
Hook::exec('actionAdminControllerSetMedia');
$moduleJsFiles = array_unique($shim->js_files);

// 3. Ouvrir les paths Smarty pour les HelperForm/HelperCalendar dans les hooks
$boTheme = $employee->bo_theme ?: 'default';
Context::getContext()->smarty->addTemplateDir(_PS_BO_ALL_THEMES_DIR_ . $boTheme . '/template/');
```

### Étape 2 — Routing

Créer `src/PrestaShopBundle/Resources/config/routing/admin/dashboard.yml` :

```yaml
admin_dashboard:
  path: /
  methods: [ GET, POST ]
  defaults:
    _controller: 'PrestaShopBundle\Controller\Admin\DashboardController::indexAction'
    _legacy_controller: AdminDashboard
    _legacy_link: AdminDashboard
  options:
    expose: true
```

Ajouter dans `src/PrestaShopBundle/Resources/config/routing/admin.yml` :

```yaml
_admin_dashboard_routing:
  resource: "admin/dashboard.yml"
  prefix: /dashboard
```

### Étape 3 — Template Twig

Créer `src/PrestaShopBundle/Resources/views/Admin/Dashboard/index.html.twig` en portant `view.tpl` :
- Zones : `{{ hookDashboardZoneOne|raw }}` etc. — les hooks retournent du HTML
- Calendrier : porter `calendar.tpl` directement en Twig (pas de `HelperCalendar`)
- Variables JS (`dashboard_ajax_url`, `adminstats_ajax_url`, `translated_dates`) : dans `{% block extra_javascripts %}`
- Assets : `asset('js/vendor/d3.v3.min.js')`, `asset('themes/default/js/vendor/nv.d3.min.js')`, `asset('js/admin/dashboard.js')`
- Assets modules (depuis `$shim->js_files`) : boucle Twig sur `moduleJsFiles`

### Étape 4 — Shim `AdminDashboardController`

Vider `AdminDashboardControllerCore` de toute logique de rendu et AJAX. Garder uniquement :

```php
public function __construct()
{
    // Ne pas appeler parent::__construct() — trop lourd pour un shim.
    // Cette classe existe uniquement pour que get_class($context->controller)
    // retourne 'AdminDashboardController' aux modules.
    $this->context = Context::getContext();
}
```

### Étape 5 — Vérification manuelle

- Page principale affiche les 4 widgets
- Rafraîchissement AJAX fonctionne (clic sur les boutons de date)
- Mode démo bascule correctement
- `dashboard_ajax_url` pointe vers la nouvelle route Symfony
- `getAdminLink('AdminDashboard')` retourne la nouvelle URL (via `_legacy_link`)

---

## 12. Fichiers clés

| Fichier | Pourquoi |
|---------|----------|
| `controllers/admin/AdminDashboardController.php` | Contrôleur legacy à transformer en shim |
| `js/admin/dashboard.js` | Logique JS frontend — ne pas modifier |
| `admin-dev/themes/default/template/controllers/dashboard/helpers/view/view.tpl` | Vue principale à porter en Twig |
| `admin-dev/themes/default/template/helpers/calendar/calendar.tpl` | Calendrier à porter en Twig |
| `modules/dashactivity/dashactivity.php` | Référence : pattern hookDashboardZoneOne + HelperForm |
| `src/PrestaShopBundle/Resources/config/routing/admin.yml` | Aggregateur des routes admin |
| `src/PrestaShopBundle/Controller/Admin/PrestaShopAdminController.php` | Classe de base pour le nouveau contrôleur |

---

## 13. Epics et tickets de migration

Les epics sont ordonnés par dépendance. L'Epic 1 est le seul prérequis aux autres ; les Epics 2 et 3 peuvent être menés en parallèle une fois l'Epic 1 livré.

Tailles indicatives : XS < 0,5 j — S ≈ 0,5–1 j — M ≈ 1–2 j — L ≈ 3–5 j

---

### Epic 1 — Migration du contrôleur Dashboard _(sans toucher aux modules)_

**Objectif :** faire tourner le Dashboard sur un contrôleur Symfony tout en conservant les modules intacts. Valeur technique, pas visible pour l'utilisateur.

| # | Ticket | Description | Taille |
|---|--------|-------------|--------|
| DASH-01 | Symfony controller + routing | Créer `DashboardController` étendant `PrestaShopAdminController`, routing YAML avec `_legacy_link: AdminDashboard` | S |
| DASH-02 | Template Twig principal | Porter `view.tpl` en `index.html.twig` : zones hook, variables JS, boucle assets modules | S |
| DASH-03 | Calendrier en Twig | Porter `calendar.tpl` en Twig, éliminer `HelperCalendar`, charger `date-range-picker.js` + `calendar.js` dans `extra_javascripts` | M |
| DASH-04 | Shim `AdminDashboardController` | Vider `AdminDashboardControllerCore`, ne garder que le constructeur minimal (compat `get_class()`) | XS |
| DASH-05 | QA manuelle | Vérifier : 4 widgets affichés, rafraîchissement AJAX, mode démo, `getAdminLink('AdminDashboard')` | S |

---

### Epic 2 — Modernisation du stack JavaScript

**Objectif :** sortir le JS du Dashboard du pattern legacy `addJS()` et remplacer les librairies obsolètes. Prérequis : Epic 1.

| # | Ticket | Description | Taille |
|---|--------|-------------|--------|
| DASH-06 | Bundler `dashboard.js` | Déplacer la logique dans un entry point webpack du new-theme, remplacer `addJS()` | M |
| DASH-07 | Remplacer D3v3 + NVD3 | Migrer les rendus graphiques vers une lib moderne (Chart.js, ECharts…) — impacte tous les modules `dash*` | L |
| DASH-08 | Formaliser le contrat `hookDashboardData` | Définir une interface ou un DTO pour les données retournées par les modules, documenter le contrat AJAX | M |

---

### Epic 3 — Migration des modules `dash*`

**Objectif :** éliminer les `HelperForm`, Smarty et `Db::getInstance()` dans chaque module. Prérequis : Epic 1. Parallélisable par module.

#### 3a — `dashactivity`

| # | Ticket | Description | Taille |
|---|--------|-------------|--------|
| DASH-09 | Remplacer `HelperForm` | Migrer `renderConfigForm()` vers un Symfony Form Type + Twig | M |
| DASH-10 | Templates Smarty → Twig | Porter `dashboard_zone_one.tpl` et les templates de hook en Twig | M |
| DASH-11 | Doctrine | Remplacer `Db::getInstance()` par des repositories Doctrine | M |

#### 3b — `dashtrends`

| # | Ticket | Description | Taille |
|---|--------|-------------|--------|
| DASH-12 | Templates Smarty → Twig | Porter les templates de hook en Twig | M |
| DASH-13 | Doctrine | Remplacer `Db::getInstance()` par des repositories Doctrine | M |

#### 3c — `dashgoals`

| # | Ticket | Description | Taille |
|---|--------|-------------|--------|
| DASH-14 | Entité `ConfigurationKPI` | Modéliser la table `ConfigurationKPI` comme entité Doctrine + migration | M |
| DASH-15 | Templates Smarty → Twig | Porter les templates de hook en Twig | M |
| DASH-16 | Doctrine | Remplacer `Db::getInstance()` par des repositories Doctrine | M |

#### 3d — `dashproducts`

| # | Ticket | Description | Taille |
|---|--------|-------------|--------|
| DASH-17 | Templates Smarty → Twig | Porter les templates de hook en Twig | S |
| DASH-18 | Doctrine | Remplacer `Db::getInstance()` par des repositories Doctrine | M |

---

### Epic 4 — Cleanup final

**Objectif :** supprimer tout le code de compatibilité temporaire. Prérequis : Epic 3 terminé sur tous les modules.

| # | Ticket | Description | Taille |
|---|--------|-------------|--------|
| DASH-19 | Supprimer le shim | Supprimer `AdminDashboardControllerCore` ou le retirer du routing legacy | XS |
| DASH-20 | Supprimer les assets legacy | Retirer `d3.v3.min.js`, `nv.d3.min.js` si Epic 2 (DASH-07) est livré | S |
| DASH-21 | Tests UI Playwright | Écrire les tests E2E pour la page Dashboard (affichage widgets, sélecteur de dates, mode démo) | L |

---

### Récapitulatif des dépendances

```
Epic 1 (DASH-01 → DASH-05)
  ├── Epic 2 (DASH-06 → DASH-08)   ← modernisation JS
  ├── Epic 3a (DASH-09 → DASH-11)  ← dashactivity
  ├── Epic 3b (DASH-12 → DASH-13)  ← dashtrends
  ├── Epic 3c (DASH-14 → DASH-16)  ← dashgoals
  └── Epic 3d (DASH-17 → DASH-18)  ← dashproducts
        └── Epic 4 (DASH-19 → DASH-21)  ← cleanup (attend Epic 3 complet)
```
