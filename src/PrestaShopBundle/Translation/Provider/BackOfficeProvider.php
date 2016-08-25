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
 *  @author 	PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2016 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Translation\Provider;

use Symfony\Component\Translation\MessageCatalogue;

class BackOfficeProvider extends AbstractProvider
{
    use TranslationFinderTrait;

    private $locale;

    /**
     * {@inheritdoc}
     */
    public function getDirectories()
    {
        return array($this->getResourceDirectory().'Admin');
    }

    /**
     * {@inheritdoc}
     */
    public function getTranslationDomains()
    {
        return array('Admin.%');
    }

    /**
     * {@inheritdoc}
     */
    public function getLocale()
    {
        return $this->locale;
    }

    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMessageCatalogue()
    {
        $xliffCatalogue = $this->getCatalogueFromPaths($this->getDirectories(), $this->locale);

        $databaseCatalogue = new MessageCatalogue($this->locale);

        foreach ($this->getTranslationDomains() as $translationDomain) {
            $domainCatalogue = $this->getDatabaseLoader()->load(null, $this->locale, $translationDomain);
            $databaseCatalogue->addCatalogue($domainCatalogue);
        }

        // merge database catalogue to xliff catalogue
        $xliffCatalogue->addCatalogue($databaseCatalogue);

        return $xliffCatalogue;
    }
}
