<?php

/**
 * 2007-2016 PrestaShop.
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
 * @copyright 2007-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Translation;

use PrestaShopBundle\Translation\Provider\ProviderInterface;

/**
 * This class returns a collection of translations, using locale and identifier.
 * 
 * Returns MessageCatalogue object or Translation tree array.
 */
class TranslationsFactory
{
    private $providers = array();

    /**
     * Generates extract of global Catalogue, using domain's identifiers.
     * 
     * @return MessageCatalogue A MessageCatalogue instance.
     */
    public function createCatalogue($identifier, $locale = 'en_US')
    {
        foreach ($this->providers as $provider) {
            if ($identifier === $provider->getIdentifier()) {
                return $provider->setLocale($locale)->getMessageCatalogue();
            }
        }
    }

    /**
     * Used to generate Translation tree in Back Office
     * 
     * @return array Translation tree structure.
     */
    public function createTree($identifier, $locale = 'en_US')
    {
        foreach ($this->providers as $provider) {
            if ($identifier === $provider->getIdentifier()) {
                // set locale
                $provider->setLocale($locale);

                $tree = $provider->getXliffCatalogue()->all();
                $databaseTranslations = $provider->getDatabaseCatalogue()->all();

                foreach ($databaseTranslations as $domain => $messages) {
                    foreach ($messages as $translationKey => $translationValue) {
                        $tree[$domain][$translationKey] = array(
                            // Xliff-based translation stored for reset action
                            'xlf' => $tree[$domain][$translationKey],
                            'db' => $translationValue,
                        );
                    }
                }

                ksort($tree);

                return $tree;
            }
        }
    }

   public function addProvider(ProviderInterface $provider)
   {
       $this->providers[] = $provider;
   }
}
