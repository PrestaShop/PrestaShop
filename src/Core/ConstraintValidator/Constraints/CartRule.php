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

namespace PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints;

use PrestaShop\PrestaShop\Core\ConstraintValidator\CartRuleValidator;
use Symfony\Component\Validator\Constraint;

class CartRule extends Constraint
{
    /**
     * When discount type "specific_product" is selected, but the specific product is not provided
     *
     * @var string
     */
    public $missingSpecificProductMessage = 'Specific product must be selected for this discount application type';

    /**
     * When discount type "selected_products" is selected, but there are no selected product restrictions
     *
     * @var string
     */
    public $missingProductRestrictionsMessage = 'Product restrictions must be applied for this discount application type';

    /**
     * When cart rule has no actions
     *
     * @var string
     */
    public $missingActionsMessage = 'Cart rule must have at least one action';

    /**
     * {@inheritDoc}
     */
    public function validatedBy(): string
    {
        return CartRuleValidator::class;
    }
}
