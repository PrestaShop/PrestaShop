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

namespace PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyConstraintException;

/**
 * Class AlphaIsoCode
 */
class AlphaIsoCode
{
    /**
     * @var string
     */
    private $isoCode;

    /**
     * @param string $isoCode
     *
     * @throws CurrencyConstraintException
     */
    public function __construct($isoCode)
    {
        $this->assertIsValidIsoCode($isoCode);
        $this->isoCode = $isoCode;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->isoCode;
    }

    /**
     * @param string $isoCode
     *
     * @throws CurrencyConstraintException
     */
    private function assertIsValidIsoCode($isoCode)
    {
        $regex = '/^[a-zA-Z]{2,3}$/';
        if (!is_string($isoCode) || !preg_match($regex, $isoCode)) {
            throw new CurrencyConstraintException(
                sprintf(
                    'Given iso code "%s" is not valid. It did not matched given regex %s',
                    var_export($isoCode, true),
                    $regex
                ),
                CurrencyConstraintException::INVALID_ISO_CODE
            );
        }
    }
}
