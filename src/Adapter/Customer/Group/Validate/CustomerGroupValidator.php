<?php
/*
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

namespace PrestaShop\PrestaShop\Adapter\Customer\Group\Validate;

use Group as CustomerGroup;
use PrestaShop\PrestaShop\Adapter\AbstractObjectModelValidator;
use PrestaShop\PrestaShop\Adapter\Shop\Repository\ShopRepository;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Exception\GroupConstraintException;

class CustomerGroupValidator extends AbstractObjectModelValidator
{
    /**
     * @var ShopRepository
     */
    private $shopRepository;

    public function __construct(ShopRepository $shopRepository)
    {
        $this->shopRepository = $shopRepository;
    }

    /**
     * @param CustomerGroup $customerGroup
     *
     * @throws GroupConstraintException
     *
     * @return void
     */
    public function validate(CustomerGroup $customerGroup): void
    {
        $this->validateThereIsAtLeastOneShop($customerGroup->id_shop_list);
        $this->validateShopsExists($customerGroup->id_shop_list);
        $this->validateGroupNames($customerGroup->name);
        $this->validatePriceDisplayMethod($customerGroup->price_display_method);
    }

    /**
     * @param array $shopIds
     *
     * @throws GroupConstraintException
     *
     * @return void
     */
    private function validateThereIsAtLeastOneShop(array $shopIds): void
    {
        if (empty($shopIds)) {
            throw new GroupConstraintException(
                'Customer group must be associated with at least one shop',
                GroupConstraintException::EMPTY_SHOP_LIST
            );
        }
    }

    /**
     * @param array $shopIds
     *
     * @return void
     */
    private function validateShopsExists(array $shopIds): void
    {
        foreach ($shopIds as $shopId) {
            $this->shopRepository->assertShopExists($shopId);
        }
    }

    /**
     * @param int $priceDisplayMethod
     *
     * @throws GroupConstraintException
     *
     * @return void
     */
    private function validatePriceDisplayMethod(int $priceDisplayMethod): void
    {
        switch ($priceDisplayMethod) {
            case CustomerGroup::PRICE_DISPLAY_METHOD_TAX_INCL:
            case CustomerGroup::PRICE_DISPLAY_METHOD_TAX_EXCL:
                return;
            default:
                throw new GroupConstraintException(
                    sprintf('Invalid price display method "%s"', $priceDisplayMethod),
                    GroupConstraintException::INVALID_PRICE_DISPLAY_METHOD
                );
        }
    }

    /**
     * @param string[] $names
     *
     * @throws GroupConstraintException
     *
     * @return void
     */
    private function validateGroupNames(array $names): void
    {
        if (empty($names)) {
            throw new GroupConstraintException(
                'Customer group name cannot be empty',
                GroupConstraintException::EMPTY_NAME
            );
        }
        foreach ($names as $name) {
            $this->validateGroupName($name);
        }
    }

    /**
     * @param string $name
     *
     * @throws GroupConstraintException
     *
     * @return void
     */
    private function validateGroupName(string $name): void
    {
        if (strlen($name) > 32) {
            throw new GroupConstraintException(
                sprintf('Customer group name cannot be longer than 32 characters. Got "%s"', $name),
                GroupConstraintException::NAME_TOO_LONG
            );
        }
        if (false === preg_match('/^[^<>={}]*$/u', $name)) {
            throw new GroupConstraintException(
                'Customer group name cannot contain these characters: < > = { }',
                GroupConstraintException::INVALID_NAME
            );
        }
    }
}
