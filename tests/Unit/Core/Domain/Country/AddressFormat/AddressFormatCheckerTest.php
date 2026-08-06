<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\Country\AddressFormat;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Country\AddressFormat\AddressFormatChecker;
use PrestaShop\PrestaShop\Core\Domain\Country\AddressFormat\AddressFormatCheckerInterface;
use PrestaShop\PrestaShop\Core\Domain\Country\AddressFormat\AddressFormatFieldsProviderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Unit-tests the pure Core checker against an in-memory fields provider and a
 * no-op translator — no PrestaShop bootstrap, no legacy ObjectModel reflection.
 */
class AddressFormatCheckerTest extends TestCase
{
    private AddressFormatCheckerInterface $checker;

    protected function setUp(): void
    {
        $fieldsProvider = new class() implements AddressFormatFieldsProviderInterface {
            public function getPickerClasses(): array
            {
                return ['Address', 'Country', 'State', 'Customer', 'Warehouse'];
            }

            public function getFieldsForClass(string $className): array
            {
                return [
                    'Address' => ['firstname', 'lastname', 'company', 'address1', 'postcode', 'city', 'phone'],
                    'Country' => ['name', 'iso_code'],
                    'State' => ['name', 'iso_code'],
                    'Customer' => ['firstname', 'lastname', 'email'],
                    'Warehouse' => ['name', 'reference'],
                ][$className] ?? [];
            }

            public function getRequiredFields(): array
            {
                return ['firstname', 'lastname', 'address1', 'city', 'Country:name'];
            }
        };

        $translator = new class() implements TranslatorInterface {
            public function trans(string $id, array $parameters = [], ?string $domain = null, ?string $locale = null): string
            {
                return strtr($id, $parameters);
            }

            public function getLocale(): string
            {
                return 'en';
            }
        };

        $this->checker = new AddressFormatChecker($fieldsProvider, $translator);
    }

    public function testValidFormatReturnsNoErrors(): void
    {
        $format = "firstname lastname\naddress1\npostcode city\nCountry:name";
        $errors = $this->checker->validate($format);

        $this->assertSame([], $errors, 'A format containing all required fields should validate.');
    }

    public function testFormatWithBareTokensIsValid(): void
    {
        $format = "firstname lastname\naddress1\ncity\nCountry:name";
        $errors = $this->checker->validate($format);

        $this->assertSame([], $errors);
    }

    public function testMissingRequiredFieldProducesError(): void
    {
        // Drops `firstname` — required and bare → resolves to Address:firstname.
        $format = "lastname\naddress1\ncity\nCountry:name";
        $errors = $this->checker->validate($format);

        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('firstname', $errors[0]);
    }

    public function testCustomerPrefixedTokenDoesNotSatisfyBareRequired(): void
    {
        // Customer:firstname is a different token from bare `firstname` (=Address:firstname).
        // The required entry `firstname` should remain unsatisfied.
        $format = "Customer:firstname lastname\naddress1\ncity\nCountry:name";
        $errors = $this->checker->validate($format);

        $this->assertNotEmpty($errors);
    }

    public function testDuplicateTokenProducesError(): void
    {
        $format = "firstname firstname\nlastname\naddress1\ncity\nCountry:name";
        $errors = $this->checker->validate($format);

        $this->assertNotEmpty($errors);
    }

    public function testUnknownBareFieldProducesError(): void
    {
        $format = "firstname lastname\naddress1\ncity\nCountry:name\ntotally_not_a_field";
        $errors = $this->checker->validate($format);

        $this->assertNotEmpty($errors);
    }

    public function testForbiddenClassProducesError(): void
    {
        // Manufacturer is not in the picker objects whitelist.
        $format = "firstname lastname\naddress1\ncity\nCountry:name\nManufacturer:name";
        $errors = $this->checker->validate($format);

        $this->assertNotEmpty($errors);
    }

    public function testUnknownPrefixedFieldProducesError(): void
    {
        // Country exists in the picker but `siret` does not belong to it.
        $format = "firstname lastname\naddress1\ncity\nCountry:siret";
        $errors = $this->checker->validate($format);

        $this->assertNotEmpty($errors);
    }

    public function testEmptyFormatReturnsErrors(): void
    {
        // No tokens at all → every required field is missing.
        $errors = $this->checker->validate('');

        $this->assertNotEmpty($errors);
    }

    public function testMalformedPrefixedTokenProducesError(): void
    {
        // Empty class or empty field on either side of the colon.
        $format = "firstname lastname\naddress1\ncity\nCountry:name\n:name";
        $errors = $this->checker->validate($format);

        $this->assertNotEmpty($errors);
    }
}
