<?php
/**
 * 2007-2018 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\Language\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Language\Exception\LanguageConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\LanguageException;

/**
 * Stores language's two-letter (639-1) ISO code
 */
class IsoCode
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
     */
    public function __construct($isoCode)
    {
        $this->assertIsIsoCode($isoCode);

        $this->isoCode = strtolower($isoCode);
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
     * @throws LanguageException
     */
    private function assertIsIsoCode($isoCode)
    {
        if (!is_string($isoCode) || !preg_match('/^[a-zA-Z]{2,3}$/', $isoCode)) {
            throw new LanguageConstraintException(
                sprintf('Invalid language ISO code %s supplied', var_export($isoCode, true)),
                LanguageConstraintException::INVALID_ISO_CODE
            );
        }
    }
}
