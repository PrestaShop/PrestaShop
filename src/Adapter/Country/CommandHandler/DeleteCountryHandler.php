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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Country\CommandHandler;

use Country;
use PrestaShop\PrestaShop\Adapter\Country\AbstractCountryHandler;
use PrestaShop\PrestaShop\Core\Domain\Country\Command\DeleteCountryCommand;
use PrestaShop\PrestaShop\Core\Domain\Country\CommandHandler\DeleteCountryHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\DeleteCountryException;

/**
 * Handles creation of country and address format for it
 */
class DeleteCountryHandler extends AbstractCountryHandler implements DeleteCountryHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws CountryNotFoundException
     * @throws DeleteCountryException
     */
    public function handle(DeleteCountryCommand $command): void
    {
        $country = new Country($command->getCountryId()->getValue());

        if (0 >= $country->id) {
            throw new CountryNotFoundException(sprintf('Unable to find country with id "%d" for deletion', $command->getCountryId()->getValue()));
        }

        if (!$country->delete()) {
            throw new DeleteCountryException(sprintf('Cannot delete country with id "%d"', $command->getCountryId()->getValue()), DeleteCountryException::FAILED_DELETE);
        }
    }
}
