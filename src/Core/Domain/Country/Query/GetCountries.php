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

namespace PrestaShop\PrestaShop\Core\Domain\Country\Query;

/**
 * Class is responsible for providing countries.
 */
class GetCountries
{
    /**
     * @var int
     */
    private $langId;

    /**
     * @var bool
     */
    private $active = false;

    /**
     * @var bool
     */
    private $containsStates = false;

    /**
     * @var bool
     */
    private $includeStatesList = true;

    /**
     * @param int $langId
     */
    public function __construct(int $langId)
    {
        $this->langId = $langId;
    }

    /**
     * @return int
     */
    public function getLangId(): int
    {
        return $this->langId;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $isActive
     *
     * @return GetCountries
     */
    public function setActive(bool $isActive): GetCountries
    {
        $this->active = $isActive;

        return $this;
    }

    /**
     * @return bool
     */
    public function doesContainStates(): bool
    {
        return $this->containsStates;
    }

    /**
     * @param bool $containsStates
     *
     * @return GetCountries
     */
    public function setContainsStates(bool $containsStates): GetCountries
    {
        $this->containsStates = $containsStates;

        return $this;
    }

    /**
     * @return bool
     */
    public function doesIncludeStatesList(): bool
    {
        return $this->includeStatesList;
    }

    /**
     * @var bool
     *
     * @return GetCountries
     */
    public function setIncludeStatesList(bool $includeStatesList): GetCountries
    {
        $this->includeStatesList = $includeStatesList;

        return $this;
    }
}
