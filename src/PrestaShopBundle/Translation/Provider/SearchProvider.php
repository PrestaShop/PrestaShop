<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Translation\Provider;

use Exception;
use PrestaShop\PrestaShop\Core\Exception\FileNotFoundException;
use PrestaShop\PrestaShop\Core\Translation\Exception\TranslationFilesNotFoundException;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\MessageCatalogueInterface;

/**
 * Able to search translations for a specific translation domains across multiple sources
 */
class SearchProvider extends AbstractProvider implements UseDefaultCatalogueInterface, UseModuleInterface
{
    /**
     * @var ExternalModuleLegacySystemProvider
     */
    private $externalModuleLegacySystemProvider;

    public function __construct(
        LoaderInterface $databaseLoader,
        ExternalModuleLegacySystemProvider $externalModuleLegacySystemProvider,
        $resourceDirectory
    ) {
        $this->externalModuleLegacySystemProvider = $externalModuleLegacySystemProvider;

        parent::__construct($databaseLoader, $resourceDirectory);
    }

    /**
     * {@inheritdoc}
     */
    public function getTranslationDomains()
    {
        return ['^' . preg_quote($this->domain) . '([A-Z]|$)'];
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return ['#^' . preg_quote($this->domain, '#') . '([A-Z]|\.|$)#'];
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return 'search';
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultResourceDirectory()
    {
        return $this->resourceDirectory . DIRECTORY_SEPARATOR . 'default';
    }

    public function getDefaultCatalogue($empty = true)
    {
        try {
            $defaultCatalogue = parent::getDefaultCatalogue($empty);
        } catch (TranslationFilesNotFoundException) {
            $defaultCatalogue = $this->externalModuleLegacySystemProvider->getDefaultCatalogue($empty);
            $defaultCatalogue = $this->filterCatalogue($defaultCatalogue);
        }

        return $defaultCatalogue;
    }

    /**
     * @return MessageCatalogue|MessageCatalogueInterface
     *
     * @throws FileNotFoundException
     */
    public function getXliffCatalogue()
    {
        try {
            $xliffCatalogue = parent::getXliffCatalogue();
        } catch (Exception) {
            $xliffCatalogue = $this->externalModuleLegacySystemProvider->getXliffCatalogue();
            $xliffCatalogue = $this->filterCatalogue($xliffCatalogue);
        }

        return $xliffCatalogue;
    }

    /**
     * {@inheritdoc}
     */
    public function setLocale($locale)
    {
        $this->externalModuleLegacySystemProvider->setLocale($locale);

        return parent::setLocale($locale);
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
        // return only elements whose domain matches the filters
        $filters = $this->getFilters();
        $allowedDomains = [];

        foreach ($defaultCatalogue->all() as $domain => $messages) {
            foreach ($filters as $filter) {
                if (preg_match($filter, $domain)) {
                    $allowedDomains[$domain] = $messages;
                    break;
                }
            }
        }

        $defaultCatalogue = new MessageCatalogue($this->getLocale(), $allowedDomains);

        return $defaultCatalogue;
    }
}
