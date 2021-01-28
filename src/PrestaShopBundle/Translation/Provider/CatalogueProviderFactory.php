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

namespace PrestaShopBundle\Translation\Provider;

use PrestaShopBundle\Translation\Exception\UnexpectedTranslationTypeException;
use PrestaShopBundle\Translation\Loader\DatabaseTranslationLoader;
use PrestaShopBundle\Translation\TranslationCatalogueBuilder;

class CatalogueProviderFactory
{
    /**
     * @var CatalogueProviderInterface[]
     */
    private $providers;

    public function __construct(
        DatabaseTranslationLoader $databaseTranslationLoader,
        string $resourceDirectory
    ) {
        $this->providers = [
            TranslationCatalogueBuilder::TYPE_BACK => new BackofficeCatalogueProvider(
                $databaseTranslationLoader,
                $resourceDirectory
            ),
        ];
    }

    /**
     * @param string $type
     *
     * @return CatalogueProviderInterface
     *
     * @throws UnexpectedTranslationTypeException
     */
    public function getProvider(string $type): CatalogueProviderInterface
    {
        if (!in_array($type, TranslationCatalogueBuilder::ALLOWED_TYPES)) {
            throw new UnexpectedTranslationTypeException('Unexpected type');
        }

        if (array_key_exists($type, $this->providers)) {
            return $this->providers[$type];
        }

        // This should never be thrown if every Type has his Provider defined in constructor
        throw new UnexpectedTranslationTypeException('Unexpected type');
    }
}
