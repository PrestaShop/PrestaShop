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

namespace PrestaShop\PrestaShop\Adapter\Country\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Country\AbstractCountryHandler;
use PrestaShop\PrestaShop\Core\Domain\Country\Command\ToggleCountryStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Country\CommandHandler\ToggleCountryStatusHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryException;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\UpdateCountryException;

/**
 * Handles status toggling of a country
 */
final class ToggleCountryStatusHandler extends AbstractCountryHandler implements ToggleCountryStatusHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws UpdateCountryException
     * @throws CountryException
     * @throws CountryNotFoundException
     */
    public function handle(ToggleCountryStatusCommand $command)
    {
        $country = $this->getCountry($command->getCountryId());

        if (!$this->toggleCountryStatus($country, $command->getExpectedStatus())) {
            throw new UpdateCountryException(
                sprintf('Unable to toggle country status with id "%s"', $country->id),
                UpdateCountryException::FAILED_UPDATE_STATUS
            );
        }
    }
}
