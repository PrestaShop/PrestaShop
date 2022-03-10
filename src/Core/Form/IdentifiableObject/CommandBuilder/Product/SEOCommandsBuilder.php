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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product;

use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductSeoCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\CommandBuilder;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\CommandBuilderConfig;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\CommandField;

/**
 * Builder used to build UpdateSEO
 */
class SEOCommandsBuilder implements MultiShopProductCommandsBuilderInterface
{
    /**
     * @var string
     */
    private $modifyAllNamePrefix;

    /**
     * @param string $modifyAllNamePrefix
     */
    public function __construct(string $modifyAllNamePrefix)
    {
        $this->modifyAllNamePrefix = $modifyAllNamePrefix;
    }

    public function buildCommands(
        ProductId $productId,
        array $formData,
        ShopConstraint $singleShopConstraint
    ): array {
        if (!isset($formData['seo'])) {
            return [];
        }
        $config = new CommandBuilderConfig($this->modifyAllNamePrefix);
        $config
            ->addMultiShopField('[seo][meta_title]', 'setLocalizedMetaTitles', CommandField::TYPE_ARRAY)
            ->addMultiShopField('[seo][meta_description]', 'setLocalizedMetaDescriptions', CommandField::TYPE_ARRAY)
            ->addMultiShopField('[seo][link_rewrite]', 'setLocalizedLinkRewrites', CommandField::TYPE_ARRAY)
            ->addMultiShopField(
                '[seo][redirect_option][type]',
                'setRedirectOption',
                CommandField::TYPE_STRING,
                static function (string $type, array $data): array {
                    return [
                        $type,
                        (int) ($data['seo']['redirect_option']['target']['id'] ?? 0),
                    ];
                }
            )
        ;
        $commandBuilder = new CommandBuilder($config);

        return $commandBuilder->buildCommands(
            $formData,
            new UpdateProductSeoCommand($productId->getValue(), $singleShopConstraint),
            new UpdateProductSeoCommand($productId->getValue(), ShopConstraint::allShops())
        );
    }
}
