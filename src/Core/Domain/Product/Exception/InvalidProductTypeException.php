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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Exception;

use Throwable;

/**
 * Is thrown when product type is not suitable for certain operation
 * e.g. if product with combination is expected but got standard or pack product
 */
class InvalidProductTypeException extends ProductException
{
    /**
     * Code used when expected type was standard
     */
    public const EXPECTED_STANDARD_TYPE = 10;

    /**
     * Code used when expected type was pack
     */
    public const EXPECTED_PACK_TYPE = 20;

    /**
     * Code used when expected type was virtual
     */
    public const EXPECTED_VIRTUAL_TYPE = 30;

    /**
     * Code used when expected type was combinations
     */
    public const EXPECTED_COMBINATIONS_TYPE = 40;

    /**
     * Code used when expected type was standard
     */
    public const EXPECTED_NO_COMBINATIONS_TYPE = 50;

    /**
     * Code used when trying to change a product into a pack while it is already associated with another.
     */
    public const EXPECTED_NO_EXISTING_PACK_ASSOCIATIONS = 60;

    /**
     * @param int $code
     * @param string $message
     * @param Throwable|null $previous
     */
    public function __construct($code, $message = '', Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
