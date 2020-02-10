<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\Exception;

/**
 * Class DomainConstraintException is responsible for holding exception codes which can be raised in reusable way.
 */
class DomainConstraintException extends DomainException
{
    /**
     * @var int - raised when native php email validation fails. E.g filter_var($email, FILTER_VALIDATE_EMAIL)
     */
    const INVALID_EMAIL = 1;

    /**
     * Used when invalid money amount is provided
     */
    const INVALID_MONEY_AMOUNT = 2;

    /**
     * When price reduction type is not within defined types
     */
    const INVALID_REDUCTION_TYPE = 3;

    /**
     * When price reduction percentage value is not valid
     */
    const INVALID_REDUCTION_PERCENTAGE = 4;

    /**
     * When price reduction amount value is not valid
     */
    const INVALID_REDUCTION_AMOUNT = 5;
}
