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

namespace PrestaShop\PrestaShop\Adapter\Country\Repository;

use Country;
use PrestaShop\PrestaShop\Adapter\Country\Validate\CountryValidator;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CannotAddCountryException;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CannotDeleteCountryException;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CannotEditCountryException;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Repository\AbstractObjectModelRepository;

/**
 * Provides methods to access data storage of Country
 */
class CountryRepository extends AbstractObjectModelRepository
{
    /**
     * @var CountryValidator
     */
    private $countryValidator;

    public function __construct(CountryValidator $countryValidator)
    {
        $this->countryValidator = $countryValidator;
    }

    /**
     * @param CountryId $countryId
     *
     * @throws CountryNotFoundException
     */
    public function assertCountryExists(CountryId $countryId): void
    {
        $this->assertObjectModelExists(
            $countryId->getValue(),
            'country',
            CountryNotFoundException::class
        );
    }

    /**
     * @param CountryId $countryId
     *
     * @return Country
     *
     * @throws CountryNotFoundException
     */
    public function get(CountryId $countryId): Country
    {
        /** @var Country $country */
        $country = $this->getObjectModel(
            $countryId->getValue(),
            Country::class,
            CountryNotFoundException::class
        );

        return $country;
    }

    /**
     * @param Country $country
     *
     * @return Country
     *
     * @throws CountryConstraintException
     * @throws CoreException
     */
    public function add(Country $country): Country
    {
        $this->countryValidator->validate($country);

        $this->addObjectModel($country, CannotAddCountryException::class);

        return $country;
    }

    /**
     * @param Country $country
     *
     * @return Country
     *
     * @throws CannotEditCountryException
     * @throws CoreException
     */
    public function update(Country $country): Country
    {
        $this->countryValidator->validate($country);

        $this->updateObjectModel($country, CannotEditCountryException::class);

        return $country;
    }

    public function delete(CountryId $countryId): void
    {
        $this->deleteObjectModel($this->get($countryId), CannotDeleteCountryException::class);
    }
}
