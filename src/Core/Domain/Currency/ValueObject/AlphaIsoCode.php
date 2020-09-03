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

namespace PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyConstraintException;

/**
 * Class AlphaIsoCode
 */
class AlphaIsoCode
{
    /**
     * @var string ISO Code validation pattern
     */
    const PATTERN = '/^[a-zA-Z]{2,3}$/';

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
        if (!is_string($isoCode) || !preg_match(self::PATTERN, $isoCode)) {
            throw new CurrencyConstraintException(sprintf('Given iso code "%s" is not valid. It did not matched given regex %s', var_export($isoCode, true), self::PATTERN), CurrencyConstraintException::INVALID_ISO_CODE);
        }
    }
}
