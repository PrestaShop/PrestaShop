<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
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
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Translation\Provider;

use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\Loader\LoaderInterface;

abstract class AbstractProvider implements ProviderInterface
{
    use TranslationFinderTrait;

    const DEFAULT_LOCALE = 'en-US';

    private $databaseLoader;

    protected $resourceDirectory;

    protected $locale;

    public function __construct(LoaderInterface $databaseLoader, $resourceDirectory)
    {
        $this->databaseLoader = $databaseLoader;
        $this->resourceDirectory = $resourceDirectory;
        $this->locale = self::DEFAULT_LOCALE;
    }

    /**
     * {@inheritdoc}
     */
    public function getDirectories()
    {
        return array($this->getResourceDirectory());
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function getTranslationDomains()
    {
        return array('');
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
     * Get the PrestaShop locale from real locale.
     *
     * @return string The PrestaShop locale
     */
    public function getPrestaShopLocale()
    {
        return str_replace('-', '_', $this->locale);
    }

    /**
     * {@inheritdoc}
     */
    public function getMessageCatalogue()
    {
        $messageCatalogue = $this->getDefaultCatalogue();

        $xlfCatalogue = $this->getXliffCatalogue();
        $messageCatalogue->addCatalogue($xlfCatalogue);

        $databaseCatalogue = $this->getDatabaseCatalogue();

        // Merge database catalogue to xliff catalogue
        $messageCatalogue->addCatalogue($databaseCatalogue);

        return $messageCatalogue;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultCatalogue($empty = true) {

    }

    /**
     * Get the Catalogue from xliff files only.
     *
     * @return MessageCatalogue A MessageCatalogue instance
     */
    public function getXliffCatalogue()
    {
        $xlfCatalogue = new MessageCatalogue($this->locale);

        foreach ($this->getFilters() as $filter) {
            $filteredCatalogue = $this->getCatalogueFromPaths(
                $this->getDirectories(),
                $this->locale,
                $filter
            );
            $xlfCatalogue->addCatalogue($filteredCatalogue);
        }

        return $xlfCatalogue;
    }

    /**
     * Get the Catalogue from database only.
     *
     * @param null $theme
     * @return MessageCatalogue A MessageCatalogue instance
     */
    public function getDatabaseCatalogue($theme = null)
    {
        $databaseCatalogue = new MessageCatalogue($this->locale);

        foreach ($this->getTranslationDomains() as $translationDomain) {
            $domainCatalogue = $this->getDatabaseLoader()->load(null, $this->locale, $translationDomain, $theme);

            if ($domainCatalogue instanceof MessageCatalogue) {
                $databaseCatalogue->addCatalogue($domainCatalogue);
            }
        }

        return $databaseCatalogue;
    }

    /**
     * @return string Path to app/Resources/translations/{locale}
     */
    public function getResourceDirectory()
    {
        return $this->resourceDirectory.DIRECTORY_SEPARATOR.$this->locale;
    }

    /**
     * @return LoaderInterface The database loader
     */
    public function getDatabaseLoader()
    {
        return $this->databaseLoader;
    }

    /**
     * @param MessageCatalogue $messageCatalogue
     *
     * @return MessageCatalogue Empty the catalogue
     */
    public function emptyCatalogue(MessageCatalogue $messageCatalogue)
    {
        foreach ($messageCatalogue as $domain => $messages) {
            foreach ($messages as $translationKey => $translationValue) {
                $messageCatalogue[$domain][$translationKey] = '';
            }
        }

        return $messageCatalogue;
    }
}
