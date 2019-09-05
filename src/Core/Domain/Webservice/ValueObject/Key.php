<?php
/**
 * 2007-2019 PrestaShop and Contributors
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

namespace PrestaShop\PrestaShop\Core\Domain\Webservice\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Webservice\Exception\WebserviceConstraintException;

/**
 * Encapsulates webservice key value
 */
class Key
{
    /**
     * @var string Required length of webservice key
     */
    const LENGTH = 32;

    /**
     * @var string
     */
    private $key;

    /**
     * @param string $key
     */
    public function __construct($key)
    {
        $this->assertKeyIsStringAndRequiredLength($key);

        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    private function assertKeyIsStringAndRequiredLength($key)
    {
        if (!is_string($key) || strlen($key) !== self::LENGTH) {
            throw new WebserviceConstraintException(
                sprintf(
                    'Webservice key must be string of %d characters length but %s given',
                    self::LENGTH,
                    var_export($key, true)
                ),
                WebserviceConstraintException::INVALID_KEY
            );
        }
    }
}
