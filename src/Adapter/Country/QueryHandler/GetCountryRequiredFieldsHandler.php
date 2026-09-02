<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Country\QueryHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Country\CountryRequiredFieldsProviderInterface;
use PrestaShop\PrestaShop\Core\Domain\Country\Query\GetCountryRequiredFields;
use PrestaShop\PrestaShop\Core\Domain\Country\QueryHandler\GetCountryRequiredFieldsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Country\QueryResult\CountryRequiredFields;

/**
 * Handles and provides country state requirements
 */
#[AsQueryHandler]
final class GetCountryRequiredFieldsHandler implements GetCountryRequiredFieldsHandlerInterface
{
    /** @var CountryRequiredFieldsProviderInterface */
    private $requiredFieldsProvider;

    /**
     * @param CountryRequiredFieldsProviderInterface $requiredFieldsProvider
     */
    public function __construct(CountryRequiredFieldsProviderInterface $requiredFieldsProvider)
    {
        $this->requiredFieldsProvider = $requiredFieldsProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetCountryRequiredFields $query): CountryRequiredFields
    {
        return new CountryRequiredFields(
            $this->requiredFieldsProvider->isStatesRequired($query->getCountryId()),
            $this->requiredFieldsProvider->isDniRequired($query->getCountryId())
        );
    }
}
