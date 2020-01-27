<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\OrderReturnState\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\Exception\OrderReturnStateConstraintException;

/**
 * Stores order return state's name
 */
class Name
{
    /**
     * @var string Maximum allowed length for name
     */
    const MAX_LENGTH = 255;

    /**
     * @var string
     */
    private $name;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->assertNameDoesNotExceedAllowedLength($name);
        $this->assertNameIsValid($name);

        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @throws OrderReturnStateConstraintException
     */
    private function assertNameIsValid($name)
    {
        $matchesFirstNamePattern = preg_match('/^[^0-9!<>,;?=+()@#"°{}_$%:¤|]*$/u', stripslashes($name));

        if (!$matchesFirstNamePattern) {
            throw new OrderReturnStateConstraintException(sprintf('Order return state name %s is invalid', var_export($name, true)), OrderReturnStateConstraintException::INVALID_NAME);
        }
    }

    /**
     * @param string $name
     *
     * @throws OrderReturnStateConstraintException
     */
    private function assertNameDoesNotExceedAllowedLength($name)
    {
        $name = html_entity_decode($name, ENT_COMPAT, 'UTF-8');

        $length = function_exists('mb_strlen') ? mb_strlen($name, 'UTF-8') : strlen($name);
        if (self::MAX_LENGTH < $length) {
            throw new OrderReturnStateConstraintException(sprintf('Order return  state name is too long. Max allowed length is %s', self::MAX_LENGTH), OrderReturnStateConstraintException::INVALID_NAME);
        }
    }
}
