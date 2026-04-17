<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Context;

use Country;
use PHPUnit\Framework\MockObject\MockObject;
use PrestaShop\PrestaShop\Adapter\ContextStateManager;
use PrestaShop\PrestaShop\Adapter\Country\Repository\CountryRepository;
use PrestaShop\PrestaShop\Core\Context\CountryContextBuilder;
use Tests\Unit\Core\Configuration\MockConfigurationTrait;

class CountryContextBuilderTest extends ContextBuilderTestCase
{
    use MockConfigurationTrait;

    private const EN_ID = 3;
    private const FR_ID = 5;
    private const NON_EXISTENT_LANGUAGE_ID = 42;

    /**
     * @dataProvider getCountryValues
     *
     * @param int $languageId
     * @param string $expectedName
     */
    public function testBuild(int $languageId, string $expectedName): void
    {
        $country = $this->mockCountry();
        $builder = new CountryContextBuilder(
            $this->mockCountryRepository($country),
            $this->createMock(ContextStateManager::class),
            $this->mockLanguageContext($languageId)
        );
        $builder->setCountryId(42);
        $countryContext = $builder->build();
        $this->assertEquals($country->id, $countryContext->getId());
        $this->assertEquals($country->id_zone, $countryContext->getZoneId());
        $this->assertEquals($country->id_currency, $countryContext->getCurrencyId());
        $this->assertEquals($country->iso_code, $countryContext->getIsoCode());
        $this->assertEquals($country->call_prefix, $countryContext->getCallPrefix());
        $this->assertEquals($expectedName, $countryContext->getName());
        $this->assertEquals($country->contains_states, $countryContext->containsStates());
        $this->assertEquals($country->need_identification_number, $countryContext->isIdentificationNumberNeeded());
        $this->assertEquals($country->need_zip_code, $countryContext->isZipCodeNeeded());
        $this->assertEquals($country->zip_code_format, $countryContext->getZipCodeFormat());
        $this->assertEquals($country->display_tax_label, $countryContext->isTaxLabelDisplayed());
    }

    public function getCountryValues(): iterable
    {
        yield 'english name' => [
            self::EN_ID,
            'Frog Country',
        ];

        yield 'french name' => [
            self::FR_ID,
            'France',
        ];

        yield 'default fallback use first value' => [
            self::NON_EXISTENT_LANGUAGE_ID,
            'Frog Country',
        ];
    }

    private function mockCountry(): Country|MockObject
    {
        $country = $this->createMock(Country::class);
        $country->id = 42;
        $country->id_zone = 51;
        $country->id_currency = 69;
        $country->iso_code = 'FR';
        $country->call_prefix = 33;
        $country->name = [
            self::EN_ID => 'Frog Country',
            self::FR_ID => 'France',
        ];
        $country->contains_states = true;
        $country->need_identification_number = true;
        $country->need_zip_code = true;
        $country->zip_code_format = 'NNNNN';
        $country->display_tax_label = false;

        return $country;
    }

    private function mockCountryRepository(Country|MockObject $country): CountryRepository|MockObject
    {
        $repository = $this->createMock(CountryRepository::class);
        $repository
            ->method('get')
            ->willReturn($country)
        ;

        return $repository;
    }
}
