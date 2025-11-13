                try {
                    $existing = new Product((int)$existingId);
                    $images = Image::getImages($this->defaultLangId, (int)$existingId);
                    if (empty($images) && !empty($data['image'])) {
                        $this->addProductImage($existing, $data['image']);
                    } elseif (!empty($images) && !empty($data['image'])) {
                        // Jeśli istnieje rekord obrazu, ale brakuje plików, odtwórz je
                        $firstImageId = (int)$images[0]['id_image'];
                        $imgObj = new Image($firstImageId);
                        $prodImgDir = defined('_PS_PRODUCT_IMG_DIR_') ? _PS_PRODUCT_IMG_DIR_ : _PS_PROD_IMG_DIR_;
                        $imgObj->image_format = $imgObj->image_format ?: 'jpg';
                        $imgObj->createImgFolder();
                        $targetPath = $prodImgDir . $imgObj->getImgPath() . '.' . $imgObj->image_format;
                        if (!file_exists($targetPath)) {
                            if (ImageManager::resize(__DIR__ . '/../../materialy/' . $data['image'], $targetPath)) {
                                $types = ImageType::getImagesTypes('products');
                                foreach ($types as $type) {
                                    $typePath = $prodImgDir . $imgObj->getImgPath() . '-' . stripslashes($type['name']) . '.' . $imgObj->image_format;
                                    ImageManager::resize(__DIR__ . '/../../materialy/' . $data['image'], $typePath, (int) $type['width'], (int) $type['height']);
                                }
                                echo "  ✓ Odtworzono pliki obrazu dla: {$data['name']}\n";
                            }
                        }
                    }
                    $currentQty = (int) StockAvailable::getQuantity((int)$existingId, 0);
                    if ($currentQty !== (int)$data['quantity']) {
                        StockAvailable::setQuantity((int)$existingId, 0, (int)$data['quantity']);
                    }
                } catch (Exception $e) {
{
    private $defaultLangId;
    private $defaultShopId;
    private $defaultCategoryId;
    private $taxRuleGroupId;
    
    public function __construct()
    {
        $this->defaultLangId = (int)Configuration::get('PS_LANG_DEFAULT');
        $this->defaultShopId = (int)Configuration::get('PS_SHOP_DEFAULT');
        
        // Utworzenie kategorii głównej
        $this->defaultCategoryId = $this->createCategory();
        
        // Sklep bez VAT (rachunek) – nie przypisujemy żadnych reguł podatkowych
        $this->taxRuleGroupId = 0;
    }
    
    private function createCategory()
    {
        // Spróbuj znaleźć już istniejącą kategorię o tej nazwie/slugu
        $existing = Category::searchByName($this->defaultLangId, 'Suszone owoce BIO');
        if (!empty($existing)) {
            // Wybierz pierwszą pasującą
            $id = (int)$existing[0]['id_category'];
            $cat = new Category($id);
            // Zweryfikuj slug
            $slug = isset($cat->link_rewrite[$this->defaultLangId]) ? $cat->link_rewrite[$this->defaultLangId] : '';
            if ($slug === 'suszone-owoce-bio') {
                echo "✓ Użyto istniejącej kategorii: Suszone owoce BIO (ID: {$id})\n";
                return $id;
            }
        }

        $category = new Category();
        $category->name = [
            $this->defaultLangId => 'Suszone owoce BIO'
        ];
        $category->link_rewrite = [
            $this->defaultLangId => 'suszone-owoce-bio'
        ];
        $category->description = [
            $this->defaultLangId => 'Ekologiczne suszone owoce i przetwory z polskich sadów'
        ];
        $category->id_parent = 2; // Home category
        $category->active = 1;
        
        if ($category->add()) {
            echo "✓ Utworzono kategorię: Suszone owoce BIO (ID: {$category->id})\n";
            return $category->id;
        }
        
        return 2; // Fallback do Home
    }
    
    // Brak tworzenia reguł podatkowych – firma wystawia rachunki (bez VAT)
    
    public function importProducts()
    {
        $products = $this->getProductsData();
        $importedCount = 0;
        
        foreach ($products as $productData) {
            if ($this->createProduct($productData)) {
                $importedCount++;
            }
        }
        
        echo "\n✓ Zaimportowano {$importedCount} z " . count($products) . " produktów\n";
    }
    
    private function getProductsData()
    {
        return [
            // Suszone owoce
            [
                'name' => 'Jabłka suszone BIO',
                'reference' => 'VN-JABLKA-100',
                'description_short' => 'Ekologiczne suszone jabłka z polskich sadów. Bez cukru i konserwantów.',
                'description' => 'Nasze suszone jabłka BIO pochodzą z certyfikowanych ekologicznych sadów. Suszone naturalnie, zachowują wszystkie wartości odżywcze i intensywny smak świeżych jabłek. Idealny zdrowy przekąska dla całej rodziny. Produkt certyfikowany przez GIJHARS.<br><br><strong>Skład:</strong> Jabłka suszone* 100% (*z ekologicznej uprawy)<br><strong>Wartości odżywcze (100g):</strong> Energia: 243 kcal, Tłuszcz: 0,3g, Węglowodany: 59g (w tym cukry: 57g), Białko: 0,9g, Sól: 0,02g<br><strong>Przechowywanie:</strong> Przechowywać w suchym i chłodnym miejscu.',
                'price' => 15.90,
                'quantity' => 100,
                'weight' => 0.1, // 100g
                'image' => 'vita-natura-ecosusz-oferta-jablka-suszone-bio.jpg',
                'ean13' => '',
                'allergens' => 'Może zawierać śladowe ilości orzechów'
            ],
            [
                'name' => 'Gruszki suszone BIO',
                'reference' => 'VN-GRUSZKI-100',
                'description_short' => 'Aromatyczne suszone gruszki z certyfikowanych upraw ekologicznych.',
                'description' => 'Suszone gruszki BIO to doskonała alternatywa dla słodyczy. Delikatnie słodkie, aromatyczne i pełne składników odżywczych. Produkt ekologiczny bez dodatku cukru i sztucznych substancji. Certyfikat BIO.<br><br><strong>Skład:</strong> Gruszki suszone* 100% (*z ekologicznej uprawy)<br><strong>Wartości odżywcze (100g):</strong> Energia: 262 kcal, Tłuszcz: 0,4g, Węglowodany: 63g (w tym cukry: 62g), Białko: 1,2g, Sól: 0,01g<br><strong>Przechowywanie:</strong> Przechowywać w suchym i chłodnym miejscu.',
                'price' => 17.90,
                'quantity' => 100,
                'weight' => 0.1,
                'image' => 'vita-natura-ecosusz-oferta-gruszki-suszone-bio.jpg',
                'ean13' => '',
                'allergens' => 'Może zawierać śladowe ilości orzechów'
            ],
            [
                'name' => 'Maliny suszone BIO',
                'reference' => 'VN-MALINY-100',
                'description_short' => 'Intensywnie aromatyczne suszone maliny z ekologicznych upraw.',
                'description' => 'Suszone maliny BIO zachowują intensywny smak i aromat świeżych owoców. Bogate w witaminy i antyoksydanty. Idealne do płatków śniadaniowych, jogurtów, wypieków lub jako samodzielna przekąska. Certyfikat ekologiczny.<br><br><strong>Skład:</strong> Maliny suszone* 100% (*z ekologicznej uprawy)<br><strong>Wartości odżywcze (100g):</strong> Energia: 240 kcal, Tłuszcz: 4,5g, Węglowodany: 42g (w tym cukry: 39g), Błonnik: 18g, Białko: 5,2g, Sól: 0,01g<br><strong>Przechowywanie:</strong> Przechowywać w suchym i chłodnym miejscu, szczelnie zamknięte.',
                'price' => 24.90,
                'quantity' => 50,
                'weight' => 0.1,
                'image' => 'vita-natura-ecosusz-oferta-maliny-suszone-bio.jpg',
                'ean13' => '',
                'allergens' => 'Może zawierać śladowe ilości orzechów'
            ],
            [
                'name' => 'Czereśnie suszone BIO',
                'reference' => 'VN-CZERESNIE-100',
                'description_short' => 'Soczyste suszone czereśnie o intensywnym smaku.',
                'description' => 'Suszone czereśnie BIO to wyjątkowy przysmak dla miłośników naturalnych słodkości. Delikatnie słodkie z lekką kwaskowatością. Doskonałe do ciast, deserów lub jako zdrowa przekąska. Produkt certyfikowany.<br><br><strong>Skład:</strong> Czereśnie suszone* 100% (*z ekologicznej uprawy)<br><strong>Wartości odżywcze (100g):</strong> Energia: 253 kcal, Tłuszcz: 0,3g, Węglowodany: 63g (w tym cukry: 51g), Białko: 1,4g, Sól: 0,02g<br><strong>Przechowywanie:</strong> Przechowywać w suchym i chłodnym miejscu.',
                'price' => 22.90,
                'quantity' => 50,
                'weight' => 0.1,
                'image' => 'vita-natura-ecosusz-oferta-czeresnie-suszone-bio.jpg',
                'ean13' => '',
                'allergens' => 'Może zawierać pestki'
            ],
            [
                'name' => 'Śliwki suszone BIO',
                'reference' => 'VN-SLIWKI-100',
                'description_short' => 'Naturalnie suszone śliwki wspierające trawienie.',
                'description' => 'Ekologiczne suszone śliwki znane ze swoich właściwości wspierających trawienie. Bogate w błonnik i składniki mineralne. Produkt naturalny, bez dodatku cukru. Certyfikat BIO.<br><br><strong>Skład:</strong> Śliwki suszone* 100% (*z ekologicznej uprawy)<br><strong>Wartości odżywcze (100g):</strong> Energia: 240 kcal, Tłuszcz: 0,4g, Węglowodany: 57g (w tym cukry: 38g), Błonnik: 7,1g, Białko: 2,2g, Sól: 0,02g<br><strong>Przechowywanie:</strong> Przechowywać w suchym i chłodnym miejscu.',
                'price' => 16.90,
                'quantity' => 100,
                'weight' => 0.1,
                'image' => 'vita-natura-ecosusz-oferta-sliwki-suszone-bio.jpg',
                'ean13' => '',
                'allergens' => 'Może zawierać pestki'
            ],
            [
                'name' => 'Mix suszonych owoców BIO (Jabłka, Gruszki, Maliny)',
                'reference' => 'VN-MIX-100',
                'description_short' => 'Zestaw trzech rodzajów suszonych owoców - jabłka, gruszki i maliny.',
                'description' => 'Nasz ekologiczny mix suszonych owoców to kompozycja jabłek, gruszek i malin. Idealna propozycja dla osób, które chcą spróbować różnych smaków. Wszystkie owoce pochodzą z certyfikowanych upraw ekologicznych.<br><br><strong>Skład:</strong> Jabłka suszone* 40%, Gruszki suszone* 40%, Maliny suszone* 20% (*z ekologicznej uprawy)<br><strong>Wartości odżywcze (100g):</strong> Energia: 251 kcal, Tłuszcz: 1,2g, Węglowodany: 59g (w tym cukry: 56g), Błonnik: 6,5g, Białko: 1,8g, Sól: 0,02g<br><strong>Przechowywanie:</strong> Przechowywać w suchym i chłodnym miejscu.',
                'price' => 19.90,
                'quantity' => 80,
                'weight' => 0.1,
                'image' => 'vita-natura-ecosusz-oferta-jablka-gruszki-maliny-suszone-bio.jpg',
                'ean13' => '',
                'allergens' => 'Może zawierać śladowe ilości orzechów'
            ],
            
            // Zioła
            [
                'name' => 'Ziele pokrzywy suszone BIO 100g',
                'reference' => 'VN-POKRZYWA-100',
                'description_short' => 'Suszone ziele pokrzywy z ekologicznych zbiorów.',
                'description' => 'Ekologiczne ziele pokrzywy suszone naturalnymi metodami. Pokrzywa znana jest ze swoich właściwości oczyszczających i remineralizujących. Idealna do zaparzania herbat ziołowych. Certyfikat BIO.<br><br><strong>Skład:</strong> Ziele pokrzywy suszone* 100% (*z ekologicznych zbiorów)<br><strong>Sposób użycia:</strong> 1-2 łyżeczki zaparzyć w 250ml wrzątku, zaparzać 10-15 minut.<br><strong>Przechowywanie:</strong> Przechowywać w suchym miejscu, szczelnie zamknięte.',
                'price' => 12.90,
                'quantity' => 100,
                'weight' => 0.1,
                'image' => 'vita-natura-ecosusz-oferta-ziele-pokrzywy-suszone-bio.jpg',
                'ean13' => '',
                'allergens' => ''
            ],
            
            // Soki i syropy
            [
                'name' => 'Sok malinowy tłoczony BIO 300ml',
                'reference' => 'VN-SOK-MALINA-300',
                'description_short' => 'Ekologiczny sok z malin tłoczony na zimno, bez dodatku cukru.',
                'description' => 'Naturalny sok malinowy tłoczony na zimno z ekologicznych malin. Zawiera 100% owoców, bez dodatku cukru, wody czy konserwantów. Bogaty w witaminy i antyoksydanty. Pasteryzowany. Certyfikat BIO.<br><br><strong>Skład:</strong> Sok z malin* 100% (*z ekologicznej uprawy)<br><strong>Wartości odżywcze (100ml):</strong> Energia: 180 kJ / 43 kcal, Tłuszcz: 0,2g, Węglowodany: 9,5g (w tym cukry: 9,3g), Białko: 0,5g, Sól: 0,01g<br><strong>Przechowywanie:</strong> Przechowywać w chłodnym miejscu. Po otwarciu przechowywać w lodówce i spożyć w ciągu 7 dni.',
                'price' => 18.90,
                'quantity' => 50,
                'weight' => 0.35,
                'image' => 'Ekologiczny-Sok-malinowy-tloczony-EcoSusz-300-ml.jpg',
                'ean13' => '',
                'allergens' => ''
            ],
            [
                'name' => 'Syrop malinowy BIO 300ml',
                'reference' => 'VN-SYROP-MALINA-300',
                'description_short' => 'Naturalny syrop malinowy bez konserwantów.',
                'description' => 'Ekologiczny syrop malinowy z polskich malin. Idealny do deserów, napojów, naleśników i gofrów. Produkt naturalny, pasteryzowany. Certyfikat BIO od rolnika.<br><br><strong>Skład:</strong> Sok z malin* 70%, cukier trzcinowy* 30% (*z ekologicznej uprawy)<br><strong>Wartości odżywcze (100ml):</strong> Energia: 950 kJ / 225 kcal, Tłuszcz: 0,1g, Węglowodany: 56g (w tym cukry: 55g), Białko: 0,4g, Sól: 0,01g<br><strong>Przechowywanie:</strong> Przechowywać w chłodnym i ciemnym miejscu. Po otwarciu przechowywać w lodówce.',
                'price' => 16.90,
                'quantity' => 60,
                'weight' => 0.35,
                'image' => 'Ekologiczny-syrop-malinowy-300-ml-od-rolnika-Marka-bez-marki.jpg',
                'ean13' => '',
                'allergens' => ''
            ],
            
            // Przetwory
            [
                'name' => 'Konfitura z czarnej maliny BIO bez cukru',
                'reference' => 'VN-KONF-MALINA',
                'description_short' => 'Konfitura z czarnej maliny słodzona sokiem z agawy.',
                'description' => 'Ekologiczna konfitura z czarnej maliny bez dodatku cukru białego. Słodzona naturalnym sokiem z agawy. Intensywny smak i aromat dzikich malin. Doskonała do pieczywa, naleśników i deserów. Certyfikat BIO.<br><br><strong>Skład:</strong> Czarna malina* 65%, sok z agawy* 34%, substancja żelująca: pektyna, regulator kwasowości: kwas cytrynowy (*z ekologicznej uprawy)<br><strong>Wartości odżywcze (100g):</strong> Energia: 680 kJ / 162 kcal, Tłuszcz: 0,3g, Węglowodany: 38g (w tym cukry: 36g), Białko: 0,8g, Sól: 0,01g<br><strong>Przechowywanie:</strong> Przechowywać w suchym i chłodnym miejscu. Po otwarciu przechowywać w lodówce i spożyć w ciągu 14 dni.',
                'price' => 19.90,
                'quantity' => 40,
                'weight' => 0.22,
                'image' => 'Konfitura-z-czarnej-maliny-bez-CUKRU-BIO-EcoSusz.jpg',
                'ean13' => '',
                'allergens' => ''
            ],
            [
                'name' => 'Kwiat czarnego bzu nieotarty BIO 100g',
                'reference' => 'VN-BEZ-100',
                'description_short' => 'Suszone kwiaty czarnego bzu do herbat i syropów.',
                'description' => 'Ekologiczne kwiaty czarnego bzu suszone tradycyjnymi metodami. Idealne do przygotowania aromatycznej herbaty, syropu lub nalewki. Kwiaty nieotarte, pełne aromatu. Certyfikat BIO.<br><br><strong>Skład:</strong> Kwiat czarnego bzu* 100% (*z ekologicznych zbiorów)<br><strong>Sposób użycia:</strong> Do herbaty: 1 łyżka na szklankę wrzątku. Do syropu: zalać wodą z cukrem i cytryną, odstawić na 2-3 dni.<br><strong>Przechowywanie:</strong> Przechowywać w suchym miejscu, szczelnie zamknięte.',
                'price' => 14.90,
                'quantity' => 80,
                'weight' => 0.1,
                'image' => 'Kwiat-czarnego-bzu-nieotarty-BIO-100-g.jpg',
                'ean13' => '',
                'allergens' => ''
            ],
            [
                'name' => 'Ocet jabłkowy cydrowy BIO 500ml',
                'reference' => 'VN-OCET-500',
                'description_short' => 'Naturalny ocet jabłkowy z ekologicznych jabłek.',
                'description' => 'Ekologiczny ocet jabłkowy cydrowy produkowany z polskich jabłek. Niepasteryzowany, niefiltrowany - zawiera naturalną matkę octową. Bogaty w enzymy i kwasy organiczne. Idealny do sałatek, marynat i napojów zdrowotnych. Certyfikat BIO.<br><br><strong>Skład:</strong> Ocet jabłkowy* (*z ekologicznych jabłek), zawiera naturalnie występującą matkę octową<br><strong>Kwasowość:</strong> 5%<br><strong>Sposób użycia:</strong> Do sałatek, marynat, napojów (1 łyżka na szklankę wody).<br><strong>Przechowywanie:</strong> Przechowywać w ciemnym i chłodnym miejscu. Naturalny osad nie jest wadą produktu.',
                'price' => 16.90,
                'quantity' => 60,
                'weight' => 0.55,
                'image' => 'Ocet-jablkowy-cydrowy-BIO-500ml.webp',
                'ean13' => '',
                'allergens' => ''
            ],
        ];
    }
    
    private function createProduct($data)
    {
        try {
            // Sprawdź czy produkt już istnieje
            $existingId = Product::getIdByReference($data['reference']);
            if ($existingId) {
                echo "⚠ Produkt już istnieje: {$data['name']} (ref: {$data['reference']})\n";
                return false;
            }
            
            $product = new Product();
            
            // Podstawowe dane
            $product->reference = $data['reference'];
            $product->name = [$this->defaultLangId => $data['name']];
            $product->link_rewrite = [$this->defaultLangId => Tools::str2url($data['name'])];
            
            // Opisy
            $product->description_short = [$this->defaultLangId => $data['description_short']];
            $product->description = [$this->defaultLangId => $data['description']];
            
            // Kategoria i cena
            $product->id_category_default = $this->defaultCategoryId;
            $product->price = $data['price'];
            $product->id_tax_rules_group = 0; // bez VAT
            
            // Stan magazynowy
            $product->quantity = $data['quantity'];
            $product->out_of_stock = 2; // Domyślne zachowanie sklepu
            
            // Waga
            $product->weight = $data['weight'];
            
            // EAN
            if (!empty($data['ean13'])) {
                $product->ean13 = $data['ean13'];
            }
            
            // Status
            $product->active = 1;
            $product->available_for_order = 1;
            $product->show_price = 1;
            $product->visibility = 'both';
            
            // Dodatkowe pola
            $product->condition = 'new';
            $product->state = 1;
            
            if ($product->add()) {
                // Przypisz do kategorii
                $product->addToCategories([$this->defaultCategoryId]);
                
                // Aktualizuj pozycję w kategorii
                $product->updateCategories([$this->defaultCategoryId]);
                
                // Dodaj zdjęcie jeśli istnieje
                if (!empty($data['image'])) {
                    $this->addProductImage($product, $data['image']);
                }
                
                // Utwórz StockAvailable
                StockAvailable::setQuantity($product->id, 0, $data['quantity']);
                
                echo "✓ Utworzono produkt: {$data['name']} (ID: {$product->id})\n";
                return true;
            } else {
                echo "✗ Błąd podczas tworzenia produktu: {$data['name']}\n";
                return false;
            }
            
        } catch (Exception $e) {
            echo "✗ Wyjątek podczas tworzenia produktu {$data['name']}: {$e->getMessage()}\n";
            return false;
        }
    }
    
    private function addProductImage($product, $imageName)
    {
        $imagePath = __DIR__ . '/../../materialy/' . $imageName;
        
        if (!file_exists($imagePath)) {
            echo "  ⚠ Nie znaleziono zdjęcia: {$imageName}\n";
            return false;
        }
        
        try {
            $image = new Image();
            $image->id_product = $product->id;
            $image->position = 1;
            $image->cover = 1;
            
            if ($image->add()) {
                // Katalog obrazów produktów (zgodność wsteczna)
                $prodImgDir = defined('_PS_PRODUCT_IMG_DIR_') ? _PS_PRODUCT_IMG_DIR_ : _PS_PROD_IMG_DIR_;
                
                // Ustal format docelowy (domyślnie jpg)
                $image->image_format = $image->image_format ?: 'jpg';
                $targetPath = $prodImgDir . $image->getImgPath() . '.' . $image->image_format;
                
                if (ImageManager::resize($imagePath, $targetPath)) {
                    // Wygeneruj miniatury
                    $types = ImageType::getImagesTypes('products');
                    foreach ($types as $type) {
                        $typePath = $prodImgDir . $image->getImgPath() . '-' . stripslashes($type['name']) . '.' . $image->image_format;
                        ImageManager::resize($imagePath, $typePath, (int) $type['width'], (int) $type['height']);
                    }
                    echo "  ✓ Dodano zdjęcie: {$imageName}\n";
                    return true;
                }
            }
        } catch (Exception $e) {
            echo "  ✗ Błąd podczas dodawania zdjęcia: {$e->getMessage()}\n";
        }
        
        return false;
    }
}

// Uruchomienie importu
echo "=== Import produktów Vita Natura / EcoSusz ===\n\n";

try {
    $importer = new VitaNaturaProductImporter();
    $importer->importProducts();
    echo "\n=== Import zakończony ===\n";
} catch (Exception $e) {
    echo "BŁĄD: " . $e->getMessage() . "\n";
    exit(1);
}
