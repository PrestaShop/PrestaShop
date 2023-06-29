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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use DateTimeImmutable;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Query\GetCartRuleForEditing;
use PrestaShop\PrestaShop\Core\Domain\CartRule\QueryResult\CartRuleForEditing;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Reduction;
use PrestaShop\PrestaShop\Core\Shop\ShopContextInterface;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime as DateTimeUtil;

class CartRuleFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var CommandBusInterface
     */
    private $queryBus;

    /**
     * @var ShopConfigurationInterface
     */
    private $configuration;

    private ShopContextInterface $shopContext;

    public function __construct(
        CommandBusInterface $queryBus,
        ShopConfigurationInterface $configuration,
        ShopContextInterface $shopContext
    ) {
        $this->queryBus = $queryBus;
        $this->configuration = $configuration;
        $this->shopContext = $shopContext;
    }

    /**
     * {@inheritDoc}
     */
    public function getData($id)
    {
        /** @var CartRuleForEditing $editableCartRule */
        $editableCartRule = $this->queryBus->handle(new GetCartRuleForEditing($id));

        //@todo: finish up in a dedicated PR when EditCartRuleCommand is introduced
        return [
            'information' => [
                'name' => $editableCartRule->getInformation()->getLocalizedNames(),
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getDefaultData()
    {
        $now = new DateTimeImmutable();

        return [
            'information' => [
                'highlight' => false,
                'partial_use' => true,
                'priority' => 1,
                'active' => true,
            ],
            'conditions' => [
                'valid_date_range' => [
                    'from' => $now->format(DateTimeUtil::DEFAULT_DATETIME_FORMAT),
                    'to' => $now->modify('+1 month')->format(DateTimeUtil::DEFAULT_DATETIME_FORMAT),
                ],
                'minimum_amount' => [
                    'amount' => 0,
                    'currency' => (int) $this->configuration->get('PS_CURRENCY_DEFAULT'),
                    'tax_included' => false,
                    'shipping_included' => false,
                ],
                'total_available' => 1,
                'available_per_user' => 1,
                'restrictions' => [],
                'customer' => [],
                'shop_association' => $this->shopContext->getContextShopIds(),
            ],
            'actions' => [
                'free_shipping' => false,
                'discount' => [
                    'reduction' => [
                        'value' => 0,
                        'type' => Reduction::TYPE_PERCENTAGE,
                        'currency' => (int) $this->configuration->get('PS_CURRENCY_DEFAULT'),
                        'tax_included' => true,
                    ],
                    'specific_product' => [],
                    'apply_to_discounted_products' => true,
                ],
                'gift_product' => [],
            ],
        ];
    }
}
