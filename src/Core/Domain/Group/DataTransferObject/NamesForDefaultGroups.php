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

namespace PrestaShop\PrestaShop\Core\Domain\Group\DataTransferObject;

/**
 * Class DefaultGroups.
 */
class NamesForDefaultGroups
{
    /**
     * @var string
     */
    private $visitorsGroupName;

    /**
     * @var string
     */
    private $guestsGroupName;

    /**
     * @var string
     */
    private $customersGroupName;

    /**
     * @param string $visitorsGroupName
     * @param string $guestsGroupName
     * @param string $customersGroupName
     */
    public function __construct($visitorsGroupName, $guestsGroupName, $customersGroupName)
    {
        $this->visitorsGroupName = $visitorsGroupName;
        $this->guestsGroupName = $guestsGroupName;
        $this->customersGroupName = $customersGroupName;
    }

    /**
     * @return string
     */
    public function getVisitorsGroupName()
    {
        return $this->visitorsGroupName;
    }

    /**
     * @return string
     */
    public function getGuestsGroupName()
    {
        return $this->guestsGroupName;
    }

    /**
     * @return string
     */
    public function getCustomersGroupName()
    {
        return $this->customersGroupName;
    }
}
