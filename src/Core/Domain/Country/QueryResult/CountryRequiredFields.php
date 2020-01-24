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

namespace PrestaShop\PrestaShop\Core\Domain\Country\QueryResult;

/**
 * Stores data about address fields which are required by country
 */
class CountryRequiredFields
{
    /** @var bool */
    private $stateRequired;

    /** @var bool */
    private $dniRequired;

    /**
     * @param bool $stateRequired
     * @param bool $dniRequired
     */
    public function __construct(bool $stateRequired, bool $dniRequired)
    {
        $this->stateRequired = $stateRequired;
        $this->dniRequired = $dniRequired;
    }

    /**
     * @return bool
     */
    public function isStateRequired(): bool
    {
        return $this->stateRequired;
    }

    /**
     * @return bool
     */
    public function isDniRequired(): bool
    {
        return $this->dniRequired;
    }
}
