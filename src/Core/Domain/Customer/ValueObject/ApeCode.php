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

namespace PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerConstraintException;

/**
 * Every business in France is classified under an activity code
 * entitled APE - Activite Principale de l’Entreprise
 */
class ApeCode
{
    /**
     * @var string
     */
    private $code;

    /**
     * @param $code
     */
    public function __construct($code)
    {
        $this->assertIsApeCode($code);

        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->code;
    }

    private function assertIsApeCode($code)
    {
        if (is_string($code) && empty($code)) {
            return;
        }

        $isApeCode = is_string($code) && (bool) preg_match('/^\d{3,4}[a-zA-Z]{1}$/', $code);

        if (!$isApeCode) {
            throw new CustomerConstraintException(
                sprintf('Invalid ape code %s provided', var_export($code, true)),
                CustomerConstraintException::INVALID_APE_CODE
            );
        }
    }
}
