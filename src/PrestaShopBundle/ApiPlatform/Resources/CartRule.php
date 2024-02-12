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

namespace PrestaShopBundle\ApiPlatform\Resources;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Command\EditCartRuleCommand;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Query\GetCartRuleForEditing;
use PrestaShopBundle\ApiPlatform\Metadata\CQRSGet;
use PrestaShopBundle\ApiPlatform\Metadata\CQRSUpdate;
use PrestaShopBundle\ApiPlatform\Processor\CommandProcessor;

#[ApiResource(
    operations: [
        new CQRSUpdate(
            uriTemplate: '/cart-rule/{cartRuleId}',
            processor: CommandProcessor::class,
            extraProperties: [
                'CQRSCommandMapping' => [
                    '[minimumAmount][value]' => '[minimumAmount][minimumAmount]',
                ],
            ],
            CQRSCommand: EditCartRuleCommand::class,
            CQRSQuery: GetCartRuleForEditing::class
        ),
        new CQRSGet(
            uriTemplate: '/cart-rule/{cartRuleId}',
            CQRSQuery: GetCartRuleForEditing::class
        ),
    ],
)]

class CartRule
{
    #[ApiProperty(identifier: true)]
    public int $cartRuleId;

    public string $description;

    public string $code;

    public CartRuleMinimumAmount $minimumAmount;

    public bool $minimumAmountShippingIncluded;

    public int $customerId;

    public array $localizedNames;

    public bool $highlightInCart;

    public bool $allowPartialUse;

    public int $priority;

    public bool $active;

    public array $validityDateRange;

    public int $totalQuantity;

    public int $quantityPerUser;

    public CartRuleAction $cartRuleAction;
}
