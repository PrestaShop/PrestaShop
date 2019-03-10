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

use PrestaShopBundle\Translation\Exception\UnsupportedLocaleException;
use PrestaShopBundle\Translation\Extractor\LegacyModuleExtractorInterface;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Translation\MessageCatalogue;
use Exception;

/**
 * Be able to retrieve information from legacy translation files
 */
class ExternalModuleLegacySystemProvider extends AbstractProvider implements UseDefaultCatalogueInterface, SearchProviderInterface
{
    /**
     * @var SearchProviderInterface the module provider
     */
    private $moduleProvider;

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

    public function __construct(
        LoaderInterface $databaseLoader,
        $resourceDirectory,
        LoaderInterface $legacyFileLoader,
        LegacyModuleExtractorInterface $legacyModuleExtractor,
        SearchProviderInterface $moduleProvider
    ) {
        $this->moduleProvider = $moduleProvider;
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
        $defaultCatalogue = new MessageCatalogue($this->locale);

        try {
            $defaultCatalogue = $this->moduleProvider
                ->setModuleName($this->moduleName)
                ->setLocale($this->locale)
                ->getDefaultCatalogue()
            ;
        } catch (Exception $exception) {
            // Do nothing if xliff files doesn't exists
        }

        try {
            $additionalDefaultCatalogue = $this->legacyModuleExtractor->extract($this->moduleName, $this->locale);
            $defaultCatalogue->addCatalogue($additionalDefaultCatalogue);
        } catch (UnsupportedLocaleException $exception) {
            // Do nothing as support of legacy file is deprecated
        }

        if ($empty && $this->locale !== self::DEFAULT_LOCALE) {
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

    /**
     * The extracted catalogue come from translations folder.
     * The default catalogue come from module code parsing.
     *
     * @return MessageCatalogue
     *
     * @throws \Exception
     */
    public function getLegacyCatalogue()
    {
        $legacyFilesCatalogue = new MessageCatalogue($this->locale);
        $catalogueFromPhpAndSmartyFiles = $this->getDefaultCatalogue();

        try {
            $catalogueFromLegacyTranslationFiles = $this->legacyFileLoader->load(
                $this->getDefaultResourceDirectory(),
                $this->locale,
                $this->domain
            );

            foreach (array_keys($catalogueFromPhpAndSmartyFiles->all($this->domain)) as $translationKey) {
                $legacyKey = md5($translationKey);

                if ($catalogueFromLegacyTranslationFiles->has($legacyKey, $this->domain)) {
                    $legacyFilesCatalogue->set(
                        $translationKey,
                        $catalogueFromLegacyTranslationFiles->get($legacyKey, $this->domain),
                        $this->domain
                    );
                }
            }
        } catch (UnsupportedLocaleException $exception) {
            $legacyFilesCatalogue = $catalogueFromPhpAndSmartyFiles;
        }

        return $legacyFilesCatalogue;
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
     * @return string returns the module domain
     */
    private function getModuleDomain()
    {
        return 'Modules' . Container::camelize($this->moduleName);
    }
}
