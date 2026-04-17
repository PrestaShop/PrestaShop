<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
