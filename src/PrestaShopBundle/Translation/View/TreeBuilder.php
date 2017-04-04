<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Translation\View;

use PrestaShopBundle\Translation\Factory\TranslationsFactory;
use PrestaShopBundle\Translation\Provider\AbstractProvider;
use Doctrine\Common\Util\Inflector;
use PrestaShopBundle\Translation\Provider\UseDefaultCatalogueInterface;
use Symfony\Component\Translation\MessageCatalogueInterface;

class TreeBuilder
{
    private $locale;
    private $theme;

    public function __construct($locale, $theme)
    {
        $this->locale = $locale;
        $this->theme = $theme;
    }

    public function makeTranslationArray(AbstractProvider $provider)
    {
        $provider->setLocale($this->locale);
        $catalogue = $provider->getXliffCatalogue();
        $catalogue = $this->addDefaultTranslations($provider, $catalogue);

        $translations = $catalogue->all();
        $databaseCatalogue = $provider->getDatabaseCatalogue($this->theme)->all();

        foreach ($translations as $domain => $messages) {
            $databaseDomain = str_replace('.'.$this->locale, '', $domain);

            $missingTranslations = 0;

            foreach ($messages as $translationKey => $translationValue) {
                $keyExists =
                    array_key_exists($databaseDomain, $databaseCatalogue) &&
                    array_key_exists($translationKey, $databaseCatalogue[$databaseDomain])
                ;

                $fallbackOnDefaultValue = $translationKey != $translationValue ||
                    $this->locale === str_replace('_', '-', TranslationsFactory::DEFAULT_LOCALE);
                ;
                $translations[$domain][$translationKey] = array(
                    'xlf' =>  $fallbackOnDefaultValue ? $translations[$domain][$translationKey] : '',
                    'db' => $keyExists ? $databaseCatalogue[$databaseDomain][$translationKey] : '',
                );

                if (
                    empty($translations[$domain][$translationKey]['xlf']) &&
                    empty($translations[$domain][$translationKey]['db'])
                ) {
                    $missingTranslations++;
                }
            }

            $translations[$domain]['__metadata'] = array('missing_translations' => $missingTranslations);
        }

        ksort($translations);

        return $translations;
    }

    /**
     * @return array
     */
    public function makeTranslationsTree($catalogue)
    {
        $translationsTree = array();
        $flippedUnbreakableWords = array_flip($this->getUnbreakableWords());

        foreach ($catalogue as $domain => $messages) {
            $unbreakableDomain = $this->makeDomainUnbreakable($domain);

            $tableisedDomain = Inflector::tableize($unbreakableDomain);
            list($basename) = explode('.', $tableisedDomain);
            $parts = array_reverse(explode('_', $basename));

            $subtree = &$translationsTree;

            while (count($parts) > 0) {
                $subdomain = ucfirst(array_pop($parts));
                if (array_key_exists($subdomain, $flippedUnbreakableWords)) {
                    $subdomain = $flippedUnbreakableWords[$subdomain];
                }

                if (!array_key_exists($subdomain, $subtree)) {
                    $subtree[$subdomain] = array();
                }

                $subtree = &$subtree[$subdomain];
            }

            $subtree['__messages'] = array($domain => $messages);
            if (isset($messages['__metadata'])) {
                $subtree['__fixed_length_id'] = '_' . sha1($domain);
                list($subtree['__domain']) = explode('.', $domain);
                $subtree['__metadata'] = $messages['__metadata'];
                $subtree['__metadata']['domain'] = $subtree['__domain'];
                unset($messages['__metadata']);
            }
        }

        return $translationsTree;
    }

    /**
     * There are domains containing multiple words,
     * hence these domains should not be split from those words in camelcase.
     * The latter are replaced from a list of unbreakable words.
     *
     * @param $domain
     *
     * @return string
     */
    public function makeDomainUnbreakable($domain)
    {
        $adjustedDomain = $domain;
        $unbreakableWords = $this->getUnbreakableWords();

        foreach ($unbreakableWords as $search => $replacement) {
            if (false !== strpos($domain, $search)) {
                $adjustedDomain = str_replace($search, $replacement, $domain);

                break;
            }
        }

        return $adjustedDomain;
    }

    /**
     * @return array
     */
    public function getUnbreakableWords()
    {
        return array(
            'BankWire' => 'Bankwire',
            'BlockBestSellers' => 'Blockbestsellers',
            'BlockCart' => 'Blockcart',
            'CheckPayment' => 'Checkpayment',
            'ContactInfo' => 'Contactinfo',
            'EmailSubscription' => 'Emailsubscription',
            'FacetedSearch' => 'Facetedsearch',
            'FeaturedProducts' => 'Featuredproducts',
            'LegalCompliance' => 'Legalcompliance',
            'ShareButtons' => 'Sharebuttons',
            'ShoppingCart' => 'Shoppingcart',
            'SocialFollow' => 'Socialfollow',
            'WirePayment' => 'Wirepayment',
            'BlockAdvertising' => 'Blockadvertising',
            'CategoryTree' => 'Categorytree',
            'CustomerSignIn' => 'Customersignin',
            'CustomText' => 'Customtext',
            'ImageSlider' => 'Imageslider',
            'LinkList' => 'Linklist',
            'ShopPDF' => 'ShopPdf',
        );
    }

    /*
    * @param AbstractProvider $provider
    * @param MessageCatalogueInterface $catalogue
    * @return MessageCatalogueInterface
    */
    private function addDefaultTranslations(AbstractProvider $provider, MessageCatalogueInterface $catalogue)
    {
        if (!$provider instanceof UseDefaultCatalogueInterface) {
            return $catalogue;
        }

        $catalogueWithDefault = $provider->getDefaultCatalogue();
        $catalogueWithDefault->addCatalogue($catalogue);

        return $catalogueWithDefault;
    }
}
