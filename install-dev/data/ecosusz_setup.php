<?php
/**
 * EcoSusz/Vita Natura – konfiguracja sklepu (CLI)
 * Uruchom po instalacji: php install-dev/data/ecosusz_setup.php
 */

require_once __DIR__ . '/../../config/config.inc.php';

class EcoSuszSetup
{
    private $idLang;

    public function __construct()
    {
        $this->idLang = (int) Configuration::get('PS_LANG_DEFAULT');
    }

    public function run()
    {
        echo "=== EcoSusz – konfiguracja sklepu ===\n\n";
        $this->configureTaxesAndFields();
        $this->configureInvoices();
        $this->cleanupDuplicateCategory();
        $this->consolidateBioCategories();
        $this->reorderBioCategoryPositions();
        $this->createRhdCmsPage();
        $this->addRhdLinkToFooter();
        echo "\n=== Zakończono konfigurację ===\n";
    }

    private function configureTaxesAndFields()
    {
        // Wyłącz VAT i pola firmowe/NIP
        Configuration::updateValue('PS_TAX', 0);
        Configuration::updateValue('VATNUMBER_MANAGEMENT', 0);
        Configuration::updateValue('PS_B2B_ENABLE', 0);
        Configuration::updateValue('PS_COMPANY', 0);

        echo "✓ Wyłączono podatki i pola Firma/NIP\n";
    }

    private function configureInvoices()
    {
        // Ustaw prefiks dokumentów sprzedaży na RACH/
        $prefix = 'RACH/';
        $languages = Language::getLanguages(false);
        $values = [];
        foreach ($languages as $lang) {
            $values[(int)$lang['id_lang']] = $prefix;
        }
        Configuration::updateValue('PS_INVOICE_PREFIX', $values, false, null, null);
        // Opcjonalnie można wyłączyć całkowicie generowanie faktur (zostawiamy włączone dla 'rachunku')
        // Configuration::updateValue('PS_INVOICE_ENABLE', 0);

        echo "✓ Ustawiono prefiks dokumentów na {$prefix}\n";
    }

    private function cleanupDuplicateCategory()
    {
        // Znajdź wszystkie kategorie o nazwie 'Suszone owoce BIO'
        $cats = Db::getInstance()->executeS(
            'SELECT cl.id_category FROM ' . _DB_PREFIX_ . 'category_lang cl
             WHERE cl.id_lang=' . (int) $this->idLang . ' AND cl.name="Suszone owoce BIO"'
        );
        if (empty($cats)) {
            echo "• Brak duplikatów kategorii do czyszczenia\n";
            return;
        }

        // Wybierz tę z największą liczbą produktów jako główną
        $keepers = [];
        foreach ($cats as $row) {
            $id = (int) $row['id_category'];
            $count = (int) Db::getInstance()->getValue('SELECT COUNT(*) FROM ' . _DB_PREFIX_ . 'category_product WHERE id_category=' . $id);
            $keepers[$id] = $count;
        }
        arsort($keepers);
        $keepId = (int) array_key_first($keepers);

        $deleted = 0;
        foreach ($cats as $row) {
            $id = (int) $row['id_category'];
            if ($id === $keepId) {
                continue;
            }
            $count = (int) $keepers[$id];
            if ($count > 0) {
                // nie kasujemy kategorii z produktami – zostawiamy informację
                echo "  ⚠ Kategoria ID {$id} zawiera produkty – pomiń\n";
                continue;
            }
            $cat = new Category($id);
            if (Validate::isLoadedObject($cat)) {
                if ($cat->delete()) {
                    $deleted++;
                }
            }
        }

        echo "✓ Oczyszczono duplikaty kategorii: usunięto {$deleted}, zachowano ID {$keepId}\n";
    }

    private function createRhdCmsPage()
    {
        // Sprawdź, czy strona już istnieje po tytule
        $title = 'Informacja o RHD (Rolniczy Handel Detaliczny)';
        $existing = Db::getInstance()->getValue(
            'SELECT c.id_cms FROM ' . _DB_PREFIX_ . 'cms c
             INNER JOIN ' . _DB_PREFIX_ . 'cms_lang cl ON (c.id_cms=cl.id_cms AND cl.id_lang=' . (int) $this->idLang . ')
             WHERE cl.meta_title = "' . pSQL($title) . '"'
        );
        if ($existing) {
            echo "• Strona RHD już istnieje (ID: {$existing})\n";
            return;
        }

        $content = '<h1>Informacja o RHD (Rolniczy Handel Detaliczny)</h1>'
            . '<p>Sklep prowadzony jest w ramach Rolniczego Handlu Detalicznego (RHD). ' 
            . 'Produkcja i sprzedaż odbywa się zgodnie z wymogami sanitarnymi. '
            . 'Podmiot jest zgłoszony w Państwowej Inspekcji Sanitarnej (PSSE) w Opolu Lubelskim.</p>'
            . '<p>Wystawiamy dokument sprzedaży w formie rachunku (bez VAT). Ceny w sklepie są cenami ostatecznymi brutto.</p>';

        $cms = new CMS();
        $cms->active = 1;
        $cms->id_cms_category = 1; // kategoria główna CMS
        $cms->position = 0;
        $cms->link_rewrite = [$this->idLang => 'informacja-o-rhd'];
        $cms->meta_title = [$this->idLang => $title];
        $cms->meta_description = [$this->idLang => 'Informacja o zasadach sprzedaży w ramach RHD'];
        $cms->content = [$this->idLang => $content];

        if ($cms->add()) {
            echo "✓ Utworzono stronę CMS RHD (ID: {$cms->id})\n";
        } else {
            echo "✗ Nie udało się utworzyć strony CMS RHD\n";
        }
    }

    private function consolidateBioCategories()
    {
        // Wybierz kanoniczną kategorię po slugu 'suszone-owoce-bio' lub największej liczbie produktów
        $idLang = (int) $this->idLang;
        $cats = Db::getInstance()->executeS(
            'SELECT c.id_category, cl.link_rewrite
             FROM ' . _DB_PREFIX_ . 'category c
             INNER JOIN ' . _DB_PREFIX_ . 'category_lang cl ON (cl.id_category=c.id_category AND cl.id_lang=' . $idLang . ')
             WHERE cl.name="Suszone owoce BIO"'
        );
        if (!$cats) {
            echo "• Brak kategorii do konsolidacji\n";
            return;
        }
        $keepId = null;
        foreach ($cats as $row) {
            if ($row['link_rewrite'] === 'suszone-owoce-bio') {
                $keepId = (int) $row['id_category'];
                break;
            }
        }
        if (!$keepId) {
            // wybierz tę z największą liczbą produktów
            $counts = [];
            foreach ($cats as $row) {
                $id = (int) $row['id_category'];
                $counts[$id] = (int) Db::getInstance()->getValue('SELECT COUNT(*) FROM ' . _DB_PREFIX_ . 'category_product WHERE id_category=' . $id);
            }
            arsort($counts);
            $keepId = (int) array_key_first($counts);
        }

        $moved = 0; $deleted = 0;
        foreach ($cats as $row) {
            $id = (int) $row['id_category'];
            if ($id === $keepId) { continue; }
            // Przenieś produkty do kanonicznej i ustaw jako domyślną kategorię
            $prodIds = Db::getInstance()->executeS('SELECT id_product FROM ' . _DB_PREFIX_ . 'category_product WHERE id_category=' . $id);
            foreach ($prodIds as $p) {
                $pid = (int) $p['id_product'];
                Db::getInstance()->execute('REPLACE INTO ' . _DB_PREFIX_ . 'category_product (id_category, id_product, position) VALUES (' . (int)$keepId . ', ' . $pid . ', 0)');
                Db::getInstance()->execute('UPDATE ' . _DB_PREFIX_ . 'product SET id_category_default=' . (int)$keepId . ' WHERE id_product=' . $pid);
                $moved++;
            }
            // Usuń starą kategorię
            $cat = new Category($id);
            if (Validate::isLoadedObject($cat)) {
                $cat->delete();
                $deleted++;
            }
        }
        echo "✓ Skonsolidowano kategorię BIO: przeniesiono {$moved} wpisów, usunięto {$deleted} kategorii, zachowano ID {$keepId}\n";
    }

    private function addRhdLinkToFooter()
    {
        try {
            // Zidentyfikuj stronę CMS RHD
            $idLang = (int) $this->idLang;
            $idShop = (int) Context::getContext()->shop->id;
            // Sprawdź czy zainstalowany jest moduł ps_linklist (tabele link_block*)
            $tables = Db::getInstance()->executeS('SHOW TABLES LIKE "' . pSQL(_DB_PREFIX_ . 'link_block') . '"');
            if (!$tables) {
                echo "• Moduł ps_linklist nie jest zainstalowany – pomijam link w stopce\n";
                return;
            }
            $idCms = (int) Db::getInstance()->getValue(
                'SELECT cl.id_cms FROM ' . _DB_PREFIX_ . 'cms_lang cl WHERE cl.id_lang=' . $idLang . ' AND cl.meta_title="Informacja o RHD (Rolniczy Handel Detaliczny)"'
            );
            if (!$idCms) {
                echo "• Brak strony CMS RHD do podlinkowania\n";
                return;
            }
            // Znajdź domyślny blok linków (ps_linklist). Proste podejście: najniższe ID.
            $idBlock = (int) Db::getInstance()->getValue('SELECT id_link_block FROM ' . _DB_PREFIX_ . 'link_block ORDER BY id_link_block ASC LIMIT 1');
            if (!$idBlock) {
                echo "• Moduł ps_linklist (blok stopki) nie znaleziony – pomijam\n";
                return;
            }
            // Czy link już istnieje?
            $exists = (int) Db::getInstance()->getValue(
                'SELECT COUNT(*) FROM ' . _DB_PREFIX_ . 'link_block_link lbl
                 WHERE lbl.id_link_block=' . $idBlock . ' AND lbl.id_cms=' . $idCms
            );
            if ($exists) {
                echo "• Link RHD już jest w stopce\n";
                return;
            }
            // Wstaw link do CMS
            Db::getInstance()->execute(
                'INSERT INTO ' . _DB_PREFIX_ . 'link_block_link (id_link_block, id_cms, new_window, position)
                 VALUES (' . $idBlock . ', ' . $idCms . ', 0, 0)'
            );
            $idLink = (int) Db::getInstance()->Insert_ID();
            Db::getInstance()->execute(
                'INSERT INTO ' . _DB_PREFIX_ . 'link_block_link_lang (id_link, id_lang, name, description)
                 VALUES (' . $idLink . ', ' . $idLang . ', "Informacja o RHD", "Sprzedaż w ramach RHD – szczegóły")'
            );
            Db::getInstance()->execute(
                'INSERT IGNORE INTO ' . _DB_PREFIX_ . 'link_block_shop (id_link_block, id_shop) VALUES (' . $idBlock . ', ' . $idShop . ')'
            );
            echo "✓ Dodano link RHD do stopki (ps_linklist)\n";
        } catch (Exception $e) {
            echo "• Nie udało się dodać linku RHD do stopki: " . $e->getMessage() . "\n";
        }
    }

    private function reorderBioCategoryPositions()
    {
        $idLang = (int) $this->idLang;
        // Znajdź kanoniczną kategorię po slugu
        $keepId = (int) Db::getInstance()->getValue(
            'SELECT cl.id_category FROM ' . _DB_PREFIX_ . 'category_lang cl WHERE cl.id_lang=' . $idLang . ' AND cl.link_rewrite="suszone-owoce-bio"'
        );
        if (!$keepId) {
            echo "• Brak kategorii do zmiany kolejności\n";
            return;
        }
        // Pobierz produkty w kategorii posortowane po nazwie
        $products = Db::getInstance()->executeS(
            'SELECT cp.id_product, pl.name FROM ' . _DB_PREFIX_ . 'category_product cp
             INNER JOIN ' . _DB_PREFIX_ . 'product_lang pl ON (pl.id_product=cp.id_product AND pl.id_lang=' . $idLang . ')
             WHERE cp.id_category=' . $keepId . '
             ORDER BY pl.name ASC'
        );
        $pos = 0; $updated = 0;
        foreach ($products as $row) {
            $updated += (int) Db::getInstance()->update('category_product', ['position' => $pos], 'id_category=' . (int)$keepId . ' AND id_product=' . (int)$row['id_product']);
            $pos++;
        }
        // Uporządkuj pozycje na wszelki wypadek
        Product::cleanPositions($keepId);
        echo "✓ Ustawiono kolejność produktów w BIO (alfabetycznie): {$updated} pozycji\n";
    }
}

try {
    $s = new EcoSuszSetup();
    $s->run();
} catch (Exception $e) {
    echo 'BŁĄD: ' . $e->getMessage() . "\n";
    exit(1);
}
