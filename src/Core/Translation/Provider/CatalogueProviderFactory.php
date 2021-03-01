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
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Translation\Provider;

use PrestaShop\PrestaShop\Core\Translation\Builder\TranslationCatalogueBuilder;
use PrestaShop\PrestaShop\Core\Translation\Exception\UnexpectedTranslationTypeException;
use PrestaShopBundle\Translation\Loader\DatabaseTranslationLoader;

/**
 * This factory will return the provider matching the given 'type'.
 * If the type given doesn't match one of the known types, an exception will be thrown.
 */
class CatalogueProviderFactory
{
    /**
     * @var CatalogueLayersProviderInterface[]
     */
    private $providers = [];
    /**
     * @var DatabaseTranslationLoader
     */
    private $databaseTranslationLoader;
    /**
     * @var string
     */
    private $resourceDirectory;

    /**
     * @TODO We keep for now the dependency to PrestaShopBundle\Translation\Loader\DatabaseTranslationLoader
     *   We will create, in the core, a TranslationRepositoryInterface and inject to DatabaseTransloader the LangRepositoryInterface and TranslationRepositoryInterface as dependencies
     *   to be fully independent from PrestaShopBundle
     */
    public function __construct(
        DatabaseTranslationLoader $databaseTranslationLoader,
        string $resourceDirectory
    ) {
        $this->databaseTranslationLoader = $databaseTranslationLoader;
        $this->resourceDirectory = $resourceDirectory;
    }

    /**
     * @param string $type
     *
     * @return CatalogueLayersProviderInterface
     *
     * @throws UnexpectedTranslationTypeException
     */
    public function getProvider(string $type): CatalogueLayersProviderInterface
    {
        if (!in_array($type, TranslationCatalogueBuilder::ALLOWED_TYPES)) {
            throw new UnexpectedTranslationTypeException(sprintf('Unexpected type %s', $type));
        }

        switch ($type) {
            case TranslationCatalogueBuilder::TYPE_BACK:
                return $this->getBackofficeProvider();
        }

        // This should never be thrown if every Type has his Provider defined in constructor
        throw new UnexpectedTranslationTypeException(sprintf('Unexpected type %s', $type));
    }

    private function getBackofficeProvider(): CatalogueLayersProviderInterface
    {
        if (!array_key_exists(TranslationCatalogueBuilder::TYPE_BACK, $this->providers)) {
            $this->providers[TranslationCatalogueBuilder::TYPE_BACK] = new BackofficeCatalogueLayersProvider(
                $this->databaseTranslationLoader,
                $this->resourceDirectory
            );
        }

        return $this->providers[TranslationCatalogueBuilder::TYPE_BACK];
    }
}
