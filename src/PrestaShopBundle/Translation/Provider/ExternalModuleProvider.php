<?php
/**
 * 2007-2018 PrestaShop.
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Translation\Provider;

use PrestaShopBundle\Translation\Extractor\LegacyFileExtractorInterface;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Be able to retrieve information from legacy translation files
 */
class ExternalModuleProvider extends AbstractProvider implements UseDefaultCatalogueInterface
{
    /**
     * @var LegacyFileExtractorInterface the extractor
     */
    private $extractor;

    /**
     * @var string the module name
     */
    private $moduleName;

    public function __construct(LoaderInterface $databaseLoader, $resourceDirectory, LegacyFileExtractorInterface $extractor)
    {
        $this->extractor = $extractor;

        parent::__construct($databaseLoader, $resourceDirectory);
    }

    /**
     * {@inheritdoc}
     */
    public function getTranslationDomains()
    {
        return array(
            '^Modules' . $this->getModuleDomain() . '*',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return 'external_module';
    }

    public function setModuleName($moduleName)
    {
        $this->moduleName = $moduleName;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultCatalogue($empty = true)
    {
        $defaultCatalogue = new MessageCatalogue($this->getLocale());

        $filteredCatalogue = $this->getCatalogue(
            $this->getDefaultResourceDirectory(),
            $this->getLocale()
        );

        $defaultCatalogue->addCatalogue($filteredCatalogue);

        if ($empty) {
            $defaultCatalogue = $this->emptyCatalogue($defaultCatalogue);
        }

        return $defaultCatalogue;
    }

    public function getXliffCatalogue()
    {
        return new MessageCatalogue($this->locale);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultResourceDirectory()
    {
        return $this->resourceDirectory . DIRECTORY_SEPARATOR . $this->moduleName . DIRECTORY_SEPARATOR . 'translations' . DIRECTORY_SEPARATOR;
    }

    /**
     * {@inheritdoc}
     */
    public function getResourceDirectory()
    {
        return $this->getDefaultResourceDirectory();
    }

    private function getModuleDomain()
    {
        return ucfirst($this->moduleName);
    }

    /**
     * @param string $path a list of paths when we can look for translations
     * @param string $locale the Symfony (not the PrestaShop one) locale
     *
     * @return MessageCatalogue
     *
     * @throws \Exception
     */
    public function getCatalogue($path, $locale)
    {
        return $this->extractor->extract($path, $locale);
    }
}
