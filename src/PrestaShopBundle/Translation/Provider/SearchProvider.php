<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShopBundle\Translation\Provider;

use PrestaShop\PrestaShop\Core\Exception\FileNotFoundException;
use PrestaShopBundle\Translation\Loader\DatabaseTranslationLoader;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\MessageCatalogueInterface;

/**
 * Able to search translations for a specific translation domains across multiple sources
 */
class SearchProvider extends AbstractProvider implements UseModuleInterface
{
    /**
     * @var string[]
     */
    private $filenameFilters;

    /**
     * @var string the "modules" directory path
     */
    private $modulesDirectory;

    /**
     * @var ExternalModuleLegacySystemProvider
     */
    private $externalModuleLegacySystemProvider;

    public function __construct(
        ExternalModuleLegacySystemProvider $externalModuleLegacySystemProvider,
        DatabaseTranslationLoader $databaseLoader,
        $resourceDirectory,
        $modulesDirectory
    ) {
        $this->modulesDirectory = $modulesDirectory;
        $this->externalModuleLegacySystemProvider = $externalModuleLegacySystemProvider;

        $translationDomains = ['^' . preg_quote($this->domain) . '([A-Z]|$)'];

        $this->filenameFilters = ['#^' . preg_quote($this->domain, '#') . '([A-Z]|\.|$)#'];

        $defaultResourceDirectory = $resourceDirectory . DIRECTORY_SEPARATOR . 'default';

        parent::__construct(
            $databaseLoader,
            $resourceDirectory,
            $translationDomains,
            $this->filenameFilters,
            $defaultResourceDirectory
        );
    }

    /**
     * Get domain.
     *
     * @deprecated since 1.7.6, to be removed in the next major
     *
     * @return mixed
     */
    public function getDomain()
    {
        @trigger_error(
            __METHOD__ . ' function is deprecated and will be removed in the next major',
            E_USER_DEPRECATED
        );

        return $this->domain;
    }

    public function getDefaultCatalogue(bool $empty = true): MessageCatalogueInterface
    {
        try {
            $defaultCatalogue = parent::getDefaultCatalogue($empty);
        } catch (FileNotFoundException $e) {
            $defaultCatalogue = $this->externalModuleLegacySystemProvider->getDefaultCatalogue($empty);
            $defaultCatalogue = $this->filterCatalogue($defaultCatalogue);
        }

        return $defaultCatalogue;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilesystemCatalogue(): MessageCatalogueInterface
    {
        try {
            $xliffCatalogue = parent::getFilesystemCatalogue();
        } catch (\Exception $e) {
            $xliffCatalogue = $this->externalModuleLegacySystemProvider->getFilesystemCatalogue();
            $xliffCatalogue = $this->filterCatalogue($xliffCatalogue);
        }

        return $xliffCatalogue;
    }

    /**
     * {@inheritdoc}
     */
    public function setModuleName($moduleName)
    {
        $this->externalModuleLegacySystemProvider->setModuleName($moduleName);
    }

    /**
     * Filters the catalogue so that only domains matching the filters are kept
     *
     * @param MessageCatalogueInterface $defaultCatalogue
     *
     * @return MessageCatalogueInterface
     */
    private function filterCatalogue(MessageCatalogueInterface $defaultCatalogue)
    {
        $allowedDomains = [];

        // return only elements whose domain matches the filters
        foreach ($defaultCatalogue->all() as $domain => $messages) {
            foreach ($this->filenameFilters as $filter) {
                if (preg_match($filter, $domain)) {
                    $allowedDomains[$domain] = $messages;
                    break;
                }
            }
        }

        $defaultCatalogue = new MessageCatalogue($this->getLocale(), $allowedDomains);

        return $defaultCatalogue;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return 'search';
    }
}
