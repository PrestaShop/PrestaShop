# PrestaShop Development Guide for AI Agents

## Architecture Overview

PrestaShop is a PHP-based e-commerce platform transitioning from a legacy architecture to modern Symfony-based patterns. Understanding the dual architecture is critical.

### Three Application Contexts

PrestaShop runs **three separate Symfony kernels** (`app/AppKernel.php`):
- **AdminKernel** (`admin-dev/`) - Back office administration
- **FrontKernel** (`themes/`) - Customer-facing storefront  
- **AdminAPIKernel** (`admin-api/`) - REST API (API Platform 3.4+)

Each kernel has its own container and service definitions in `app/config/services/`.

### Legacy vs Modern Architecture

**Legacy System** (classes/, controllers/, override/):
- `ObjectModel` base class for entities (e.g., `Product`, `Customer`, `Order`) - uses custom ORM, not Doctrine
- Legacy controllers in `controllers/` and `admin-dev/`
- Direct database access via `Db::getInstance()`
- Hook system via `Hook::exec()` for module integration
- Smarty templates (`.tpl` files)

**Modern Stack** (src/):
- **CQRS pattern**: Commands (`src/Core/Domain/*/Command/`) and Queries (`src/Core/Domain/*/Query/`)
- **Handlers** in `src/Adapter/*/CommandHandler/` and `src/Adapter/*/QueryHandler/` - bridge legacy and modern code
- Symfony services, Doctrine ORM (partial migration), Twig templates
- Command/Query Bus: `src/Core/CommandBus/CommandBusInterface.php`
- Symfony container accessible via `SymfonyContainer::getInstance()` or `$this->container` in controllers

**Integration Points**:
- `Context` class (`classes/Context.php`) - global state shared across legacy/modern code
- Adapters in `src/Adapter/` wrap legacy classes for Symfony services
- Both systems coexist - modules and core use both patterns

### Module System

Modules extend `classes/module/Module.php` and live in `modules/`. Key patterns:
- Install/uninstall hooks via `install()` and `uninstall()` methods
- Service injection: `$this->get('service_name')` or `$this->getContainer()`
- Override system: modules can override core classes via `override/` directories
- Module services defined in `modules/{module_name}/config/services.yml` (auto-loaded if module installed)
- Two module types: native (managed via Composer in `vendor/prestashop/`) and custom (in `modules/`)

### Hook System

Hooks are the primary extension mechanism:
- **Display hooks**: Return HTML (e.g., `displayHeader`, `displayTop`)
- **Action hooks**: Trigger events (e.g., `actionProductSave`, `actionOrderStatusUpdate`)
- Register via `$this->registerHook('hookName')` in module
- Execute via `Hook::exec('hookName', $params)` (legacy) or `HookDispatcher` service (modern)
- See `install-dev/data/xml/hook.xml` for complete hook catalog

## Development Workflows

### Docker Environment

**Required commands** (from project root):
```bash
make docker-start          # Build and start containers
make docker-sh            # Access PHP container shell
make install              # Install dependencies + build assets
make install-prestashop   # Fresh database installation
```

**Container access**:
- Shop: http://localhost:8001
- Admin: http://localhost:8001/admin-dev (demo@prestashop.com / Correct Horse Battery Staple)
- MailDev: http://localhost:1080
- Database: localhost:3306 (root / prestashop)

### Asset Building

PrestaShop uses Webpack for assets with **separate build processes**:
```bash
make assets              # Build all (admin + front themes)
make admin               # admin-default + admin-new-theme
make front               # front-core + front-classic + front-hummingbird
```

Assets are in:
- Admin: `admin-dev/themes/new-theme/` (TypeScript + SCSS)
- Themes: `themes/classic/`, `themes/hummingbird/` (JS + SCSS)
- Build scripts: `tools/assets/build.sh`

**Important**: Always rebuild assets after modifying JS/CSS/TS files.

### Testing

**Test types** (`tests/`):
- **Unit tests**: `make test-unit` - PHPUnit, isolated tests
- **Integration tests**: `make test-integration` - Behat (behavior-driven), full app context
- **UI tests**: Playwright-based (see `tests/UI/README.md`), requires npm setup

**Running specific tests**:
```bash
# Unit test single file
php -d date.timezone=UTC ./vendor/bin/phpunit -c tests/Unit/phpunit.xml tests/Unit/Core/Domain/Product/
# Behat scenarios
php -d date.timezone=UTC ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml
```

Test database is auto-created via `composer create-test-db`.

### Code Quality

**Required before commits**:
```bash
make cs-fixer            # Auto-fix PHP-CS-Fixer issues
make phpstan             # Static analysis (level 5)
make scss-fixer          # Fix SCSS formatting
make es-linter           # Fix ESLint issues
```

Configuration:
- PHP-CS-Fixer: `.php-cs-fixer.dist.php` (Symfony ruleset + custom rules)
- PHPStan: `phpstan.neon.dist` with baseline files
- Rector: `rector.php` (automated refactoring rules)

### Database Management

PrestaShop uses **custom ORM** (ObjectModel), not Doctrine everywhere:
- Legacy entities: `$product = new Product($id); $product->save();`
- No migrations - schema changes via XML definitions in modules
- Multistore support: `id_shop` field in most tables, configured via `ShopContext`

## Project Conventions

### File Organization

- **Namespaces**: PSR-4 autoloading (`PrestaShop\PrestaShop\` → `src/`, `PrestaShopBundle\` → `src/PrestaShopBundle/`)
- **Legacy classes**: No namespace, autoloaded from `classes/` (e.g., `Product`, `Cart`)
- **Controllers**: 
  - Modern: `src/PrestaShopBundle/Controller/Admin/` (Symfony controllers)
  - Legacy: `controllers/admin/`, `controllers/front/`

### Naming Patterns

- **Commands**: `{Action}{Entity}Command` (e.g., `AddProductCommand`)
- **Handlers**: `{Action}{Entity}Handler` implements `{Action}{Entity}HandlerInterface`
- **Value Objects**: `src/Core/Domain/*/ValueObject/` (e.g., `ProductId`, `Email`)
- **DTOs**: `src/Core/Domain/*/QueryResult/` for query responses

### Translation System

Use domain-based translations:
```php
$this->trans('Text to translate', [], 'Admin.Catalog.Feature')
```
Domains indicate context (Admin vs Shop, feature area). See `translations/` for catalogs.

### Security

- Admin controllers: Use `#[AdminSecurity("is_granted('read', 'PERMISSION')")]` attribute
- CSRF protection via Symfony forms
- SQL injection prevention: Always use prepared statements or `pSQL()` for legacy code
- XSS prevention: Twig auto-escapes, Smarty requires `|escape:'html':'UTF-8'`

## Common Patterns

### Adding a New Admin Page (Modern)

1. Create controller in `src/PrestaShopBundle/Controller/Admin/`
2. Define route in controller with `#[Route('/path', name: 'route_name')]`
3. Add Grid/Form in `src/PrestaShopBundle/Form/Admin/` if needed
4. Create template in `src/PrestaShopBundle/Resources/views/Admin/`
5. Register menu entry via module or SQL in `ps_tab` table

### CQRS Command Flow

```php
// 1. Create command
$command = new AddProductCommand($type, $shopId, $names);

// 2. Dispatch via bus
$productId = $this->commandBus->handle($command);

// 3. Handler processes (in src/Adapter/{Domain}/CommandHandler/)
class AddProductHandler {
    public function handle(AddProductCommand $command): ProductId {
        // Bridge to legacy Product class or use Doctrine
    }
}
```

### Module Hook Implementation

```php
public function hookDisplayHeader($params) {
    // Return HTML or void for action hooks
    return $this->display(__FILE__, 'views/templates/hook/header.tpl');
}

// Or access services
public function hookActionProductSave($params) {
    $logger = $this->get('logger');
    $logger->info('Product saved', ['id' => $params['id_product']]);
}
```

### Working with ObjectModel

```php
$product = new Product($id, $id_lang);
$product->name = 'New name';
$product->price = 19.99;
$product->save(); // Triggers SQL INSERT/UPDATE

// Multistore: Specify shops
$product->id_shop_list = [1, 2];
$product->save();
```

## Critical Gotchas

1. **Cache clearing**: Symfony cache (`var/cache/`) and Smarty cache (`var/cache/`) are separate. Use `make cc` or clear both.
2. **Container differences**: Legacy controllers use `LegacyContainer` (limited services), Symfony controllers have full container.
3. **Multiple environments**: Docker containers prefix commands with `$(PHP_CONT)` in Makefile - this runs commands inside the container as `www-data` user.
4. **Override conflicts**: Module overrides can conflict. Check with `ModuleOverrideChecker` before installation.
5. **Asset watch mode**: No hot reload - manually rebuild assets after changes or use `npm run watch` in theme directories.
6. **Database prefix**: Tables use `ps_` prefix by default (configurable). Always use `_DB_PREFIX_` constant.

## Key Files Reference

- **AppKernel**: `app/AppKernel.php` - Symfony kernel base class
- **Container builder**: `src/Adapter/ContainerBuilder.php` - Legacy container for non-Symfony contexts
- **Configuration**: `app/config/parameters.php` (generated), `.env` for Docker
- **Service definitions**: `app/config/services/{admin,front}/services_*.yml`
- **Routing**: `src/PrestaShopBundle/Resources/config/routing/` + controller annotations
- **Doctrine config**: `app/config/config_legacy_*.yml` (partial Doctrine usage)

## Shop Owner Data Requirements Checklist

### Required Business/Legal Information

When setting up a PrestaShop store, collect these administrative and financial details from the shop owner:

**Company Information:**
- [ ] Legal company name (as registered)
- [ ] Company registration number:
  - Poland: NIP (Tax ID), REGON (Statistical number), KRS (Court register) for companies
  - EU: VAT-EU number for intra-community transactions
  - Other countries: equivalent business registration numbers
- [ ] Tax identification number (VAT ID/NIP)
- [ ] Company legal form (LLC/Sp. z o.o., Corporation/S.A., Sole Proprietorship/JDG, etc.)
- [ ] Date of company registration
- [ ] Share capital amount (for limited companies)
- [ ] Management board composition (names and positions)

**Business Address:**
- [ ] Registered business address (street, number, city, postal code, country)
- [ ] Correspondence address (if different from registered)
- [ ] Warehouse/fulfillment center address(es)
- [ ] Return address for customers

**Contact Details:**
- [ ] Primary business phone number
- [ ] Customer service phone number
- [ ] Customer service email address
- [ ] General contact email (contact@)
- [ ] Business hours/availability

**Financial/Banking Information:**
- [ ] Bank account number (IBAN for EU)
- [ ] Bank name and address
- [ ] SWIFT/BIC code (for international payments)
- [ ] Currency preferences
- [ ] Payment gateway credentials (PayPal, Stripe, etc.)
- [ ] Merchant account details (if applicable)

**Tax & Accounting:**
- [ ] VAT registration status
  - Poland: VAT-UE registration for EU transactions
  - Standard VAT rate (Poland: 23%, reduced rates: 8%, 5%, 0%)
- [ ] Tax exemptions (if applicable)
- [ ] Fiscal representative details (for cross-border sales)
- [ ] Default tax rates by region/product category
- [ ] Accounting system integration requirements (e.g., Comarch, InsERT, WAPRO)
- [ ] Invoice numbering format/sequence
- [ ] Polish-specific: 
  - [ ] Split payment (MPP) eligibility status
  - [ ] Tax office details (Urząd Skarbowy name and address)
  - [ ] JPK_FA format compliance confirmation
  - [ ] OSS/IOSS registration (for EU distance sales)

**Legal & Compliance:**
- [ ] Terms & Conditions document (must comply with local consumer protection laws)
- [ ] Privacy Policy document (GDPR/RODO compliant)
- [ ] Return/Refund policy (Poland: 14-day return right for consumers)
- [ ] Cookie policy
- [ ] GDPR/RODO compliance officer contact (EU)
- [ ] Data Protection Registration number (if required)
- [ ] Business licenses/permits (industry-specific)
- [ ] Polish-specific legal requirements:
  - [ ] UOKiK (Office of Competition and Consumer Protection) compliance
  - [ ] ODR platform link (Online Dispute Resolution)
  - [ ] Complaint handling procedure
  - [ ] Information clause (klauzula informacyjna RODO)

**For Food Sales (Dried Fruits) - Additional Requirements:**
- [ ] **Sanitary Registration:**
  - [ ] GIS (Chief Sanitary Inspectorate) notification/registration number
  - [ ] GIJHARS registration (for food business operators)
  - [ ] Local Sanitary-Epidemiological Station (SANEPID) approval
- [ ] **Food Safety:**
  - [ ] HACCP certification or documentation
  - [ ] Food storage conditions documentation
  - [ ] Supplier certifications (for raw materials)
  - [ ] Product traceability system (lot/batch tracking)
- [ ] **Labeling Requirements:**
  - [ ] Product name and category
  - [ ] Ingredients list (in descending order by weight)
  - [ ] Allergen information (highlighted/bold as per EU 1169/2011)
  - [ ] Net quantity/weight
  - [ ] Best before date / Use by date format
  - [ ] Storage conditions
  - [ ] Manufacturer/distributor name and address
  - [ ] Country of origin
  - [ ] Nutritional declaration (energy, fat, carbohydrates, protein, salt per 100g)
  - [ ] Batch/lot number system
- [ ] **Certifications (if applicable):**
  - [ ] Organic certification (if selling organic products)
  - [ ] BIO label documentation
  - [ ] Fair Trade certification
  - [ ] Quality certificates (ISO, IFS, BRC)
- [ ] **Import Documentation (if importing):**
  - [ ] Customs clearance documents
  - [ ] Phytosanitary certificates (for plant products)
  - [ ] Certificate of origin
  - [ ] Import licenses (if required)
- [ ] **Insurance:**
  - [ ] Product liability insurance
  - [ ] Civil liability insurance (OC)
- [ ] **Waste Management:**
  - [ ] Packaging waste recovery system registration (BDO database in Poland)
  - [ ] Extended producer responsibility (EPR) compliance

**Shipping & Logistics:**
- [ ] Default carrier contracts (DHL, UPS, local post, etc.)
- [ ] Carrier account numbers
- [ ] Shipping origin country/zone
- [ ] Free shipping thresholds (if applicable)
- [ ] Handling time (days to process orders)

**Administrator Account:**
- [ ] Admin first name & last name
- [ ] Admin email address
- [ ] Preferred admin panel language
- [ ] Timezone for order management

**Store Configuration:**
- [ ] Default shop language(s)
- [ ] Default currency
- [ ] Store name/brand
- [ ] Store logo files (multiple sizes)
- [ ] Favicon
- [ ] Meta description for SEO
- [ ] Social media profiles (Facebook, Instagram, Twitter, etc.)

### PrestaShop Database Tables for This Data

This information gets stored in:
- Company details: `ps_configuration` (keys: `PS_SHOP_NAME`, `PS_SHOP_EMAIL`, `PS_SHOP_DETAILS`)
- Addresses: `ps_address` table (linked to shop/warehouse)
- Tax rules: `ps_tax`, `ps_tax_rule`, `ps_tax_rules_group`
- Currencies: `ps_currency`
- Carriers: `ps_carrier`
- Admin user: `ps_employee`

### Required During Installation

Minimum data needed for initial installation (`install-dev/`):
```bash
# Database
DB_SERVER, DB_NAME, DB_USER, DB_PASSWD, DB_PREFIX

# Shop basics
PS_SHOP_NAME, PS_SHOP_EMAIL
PS_COUNTRY, PS_LANGUAGE
ADMIN_MAIL, ADMIN_PASSWD, ADMIN_FIRSTNAME, ADMIN_LASTNAME

# Optional but recommended
PS_ENABLE_SSL, PS_REWRITE_ENGINE
```

See `install-dev/data/xml/` for default configuration values.

## Project Assets and Materials

### Content Materials Directory (`materialy/`)

This directory contains ready-to-use assets for the EcoSusz/Vita Natura shop:

**Logo Files:**
- `ecosusz_logo-kolor/` - Full-color logo variants
  - AI, EPS, PDF source files
  - PNG/JPG exports: 500px, 1000px, 1500px sizes
- `ecosusz_logo-achromat-a/` - Black & white logo variant A
  - AI, EPS, PDF source files  
  - PNG/JPG exports: 500px, 1000px, 1500px sizes
- `ecosusz_logo-achromat-b/` - Black & white logo variant B
  - AI, EPS, PDF source files
  - PNG/JPG exports: 500px, 1000px, 1500px sizes
- `ecosusz_logo-informacje.jpg` - Logo usage guidelines
- `ecosusz-vita natura_logo.zip` - Archive with all logo files

**Product Images:**
Available product photos for dried fruits and preserves:
- `vita-natura-ecosusz-oferta-jablka-suszone-bio.jpg` - Dried organic apples
- `vita-natura-ecosusz-oferta-gruszki-suszone-bio.jpg` - Dried organic pears
- `vita-natura-ecosusz-oferta-maliny-suszone-bio.jpg` - Dried organic raspberries
- `vita-natura-ecosusz-oferta-czeresnie-suszone-bio.jpg` - Dried organic cherries
- `vita-natura-ecosusz-oferta-sliwki-suszone-bio.jpg` - Dried organic plums
- `vita-natura-ecosusz-oferta-jablka-gruszki-maliny-suszone-bio.jpg` - Mixed dried fruits
- `vita-natura-ecosusz-oferta-ziele-pokrzywy-suszone-bio.jpg` - Dried organic nettle
- `Ekologiczny-Sok-malinowy-tloczony-EcoSusz-300-ml.jpg` - Organic raspberry juice 300ml
- `Ekologiczny-syrop-malinowy-300-ml-od-rolnika-Marka-bez-marki.jpg` - Organic raspberry syrup 300ml
- `Konfitura-z-czarnej-maliny-bez-CUKRU-BIO-EcoSusz.jpg` - Sugar-free black raspberry preserve
- `Kwiat-czarnego-bzu-nieotarty-BIO-100-g.jpg` - Organic elderflower 100g
- `Ocet-jablkowy-cydrowy-BIO-500ml.webp` - Organic apple cider vinegar 500ml

**Product Descriptions:**
- `Opisy do sklepu internetowego.odt` - OpenDocument file with detailed product descriptions and copy for the online store

**Usage for PrestaShop:**
- Logo files should be uploaded via Back Office → Design → Theme & Logo
  - Recommended: Use PNG versions (1000px or 1500px) for web
  - Header logo: typically 250-350px wide
  - Favicon: Create from logo using online converter
- Product images go to: `img/p/` directory (uploaded via product management)
  - PrestaShop auto-generates thumbnails
  - Recommended size: 800x800px or larger
  - Format: JPG for photos, PNG for logos/graphics with transparency
- Product descriptions: Extract text from ODT file and add to product pages via Back Office → Catalog → Products

## Further Reading

- DevDocs: https://devdocs.prestashop-project.org/
- Build blog: https://build.prestashop-project.org/
- Module development: https://devdocs.prestashop-project.org/9/modules/
- Contributing: `CONTRIBUTING.md` in root
