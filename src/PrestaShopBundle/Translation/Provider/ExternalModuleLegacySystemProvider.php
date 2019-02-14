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

use PrestaShopBundle\Translation\Extractor\LegacyModuleExtractorInterface;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Be able to retrieve information from legacy translation files
 */
class ExternalModuleLegacySystemProvider extends AbstractProvider implements UseDefaultCatalogueInterface, SearchProviderInterface
{
    /**
     * @var LoaderInterface the translation loader from legacy files
     */
    private $legacyFileLoader;

    /**
     * @var LegacyModuleExtractorInterface the extractor
     */
    private $legacyModuleExtractor;

    /**
     * @var string the module name
     */
    private $moduleName;

    /**
     * @var string the domain name
     */
    private $domain;

    public function __construct(
        LoaderInterface $databaseLoader,
        $resourceDirectory,
        LoaderInterface $legacyFileLoader,
        LegacyModuleExtractorInterface $legacyModuleExtractor
    ) {
        $this->legacyFileLoader = $legacyFileLoader;
        $this->legacyModuleExtractor = $legacyModuleExtractor;

        parent::__construct($databaseLoader, $resourceDirectory);
    }

    /**
     * {@inheritdoc}
     */
    public function getTranslationDomains()
    {
        return ['^' . $this->getModuleDomain() . '*'];
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
        return 'external_legacy_module';
    }

    /**
     * is/should module name be part of the API?
     */
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
        $defaultCatalogue = $this->legacyModuleExtractor->extract($this->moduleName, $this->getLocale());

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
        return 'Modules' . Container::camelize($this->moduleName);
    }

    /**
     * @return MessageCatalogue
     *
     * @throws \Exception
     */
    public function getLegacyCatalogue()
    {
        $defaultCatalogue = $this->getDefaultCatalogue();
        $extractedCatalogue = $this->legacyFileLoader->load(
            $this->getDefaultResourceDirectory(),
            $this->locale,
            $this->getModuleDomain()
        );

        $legacyFileCatalogue = new MessageCatalogue($this->locale);

        foreach ($defaultCatalogue->all($this->getModuleDomain()) as $translationKey => $translation) {
            $legacyKey = md5($translationKey);

            if ($extractedCatalogue->has($legacyKey, $this->getModuleDomain())) {
                $legacyFileCatalogue->set(
                    $translationKey,
                    $extractedCatalogue->get($legacyKey, $this->getModuleDomain()),
                    $this->getModuleDomain()
                );
            }
        }

        return $legacyFileCatalogue;
    }

    /**
     * {@inheritdoc}
     */
    public function getMessageCatalogue()
    {
        $messageCatalogue = $this->getDefaultCatalogue();

        $legacyFileCatalogue = $this->getLegacyCatalogue($this->getDefaultResourceDirectory(), $this->locale);
        $messageCatalogue->add($legacyFileCatalogue);

        $databaseCatalogue = $this->getDatabaseCatalogue();
        $messageCatalogue->addCatalogue($databaseCatalogue);

        return $messageCatalogue;
    }

    /**
     * {@inheritdoc}
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }
}
