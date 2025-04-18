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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use DateTime;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Domain\Discount\Command\AddCartLevelDiscountCommand;
use PrestaShop\PrestaShop\Core\Domain\Discount\Command\AddFreeGiftDiscountCommand;
use PrestaShop\PrestaShop\Core\Domain\Discount\Command\AddFreeShippingDiscountCommand;
use PrestaShop\PrestaShop\Core\Domain\Discount\Command\AddOrderLevelDiscountCommand;
use PrestaShop\PrestaShop\Core\Domain\Discount\Command\AddProductLevelDiscountCommand;
use PrestaShop\PrestaShop\Core\Domain\Discount\ValueObject\DiscountId;
use PrestaShop\PrestaShop\Core\Domain\Discount\ValueObject\DiscountType;
use RuntimeException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\Translation\TranslatorInterface;

class DiscountFormDataHandler implements FormDataHandlerInterface
{
    public const PRODUCT_ID = 1;
    public const COMBINATION_ID = 2;

    public function __construct(
        protected readonly CommandBusInterface $commandBus,
        #[Autowire(service: 'prestashop.default.language.context')]
        protected readonly LanguageContext $defaultLanguageContext,
        protected readonly TranslatorInterface $translator,
    ) {
    }

    public function create(array $data)
    {
        $discountType = $data['type']->type;
        switch ($discountType) {
            case DiscountType::FREE_SHIPPING:
                $command = new AddFreeShippingDiscountCommand();
                $name = $this->translator->trans('On free shipping', [], 'Admin.Catalog.Feature');
                break;
            case DiscountType::CART_DISCOUNT:
                $command = new AddCartLevelDiscountCommand();
                $name = $this->translator->trans('On cart amount', [], 'Admin.Catalog.Feature');
                $command->setPercentDiscount(new DecimalNumber('50'));
                break;
            case DiscountType::PRODUCTS_DISCOUNT:
                $command = new AddProductLevelDiscountCommand();
                $name = $this->translator->trans('On products amount', [], 'Admin.Catalog.Feature');
                break;
            case DiscountType::FREE_GIFT:
                $command = new AddFreeGiftDiscountCommand();
                $command->setProductId(self::PRODUCT_ID);
                $command->setCombinationId(self::COMBINATION_ID);
                $name = $this->translator->trans('On free gift', [], 'Admin.Catalog.Feature');
                break;
            case DiscountType::ORDER_DISCOUNT:
                $command = new AddOrderLevelDiscountCommand();
                $name = $this->translator->trans('On order amount', [], 'Admin.Catalog.Feature');
                $command->setPercentDiscount(new DecimalNumber('50'));
                break;
            default:
                throw new RuntimeException('Unknown discount type ' . $discountType);
        }
        $command->setActive(true);

        // This part adds automatic values for the initial POC, this is only temporary and
        // should be removed before releasing this new page

        // Random code based on discount type
        $command->setCode(strtoupper(uniqid($discountType . '_')));
        $now = new DateTime();
        // Default name in the default language only containing the creation date
        $command->setLocalizedNames([
            $this->defaultLanguageContext->getId() => $name . ' ' . $now->format($this->defaultLanguageContext->getDateTimeFormat()),
        ]);
        $command->setTotalQuantity(100);

        /** @var DiscountId $discountId */
        $discountId = $this->commandBus->handle($command);

        return $discountId->getValue();
    }

    public function update($id, array $data)
    {
        // TODO: Implement update() method.
    }
}
