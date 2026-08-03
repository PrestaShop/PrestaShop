<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\ExtraProperty\Exception;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Exception\ExtraPropertyRegistrationFailureException;
use PrestaShop\PrestaShop\Core\ExtraProperty\Exception\ExtraPropertyException;
use PrestaShop\PrestaShop\Core\ExtraProperty\Exception\ExtraPropertyRegistryException;

/**
 * Pins the core-reason-code → domain-code mapping of fromCoreException(): the domain code
 * is what the BO controller's error-message map keys on, and the core exception must
 * always be kept as the previous exception.
 */
class ExtraPropertyRegistrationFailureExceptionTest extends TestCase
{
    /**
     * @dataProvider coreExceptionProvider
     */
    public function testFromCoreExceptionMapsTheReasonCodeAndKeepsThePrevious(ExtraPropertyException $coreException, int $expectedCode): void
    {
        $exception = ExtraPropertyRegistrationFailureException::fromCoreException($coreException, 'Failed to register.');

        $this->assertSame($expectedCode, $exception->getCode());
        $this->assertSame($coreException, $exception->getPrevious());
        $this->assertStringContainsString('Failed to register.', $exception->getMessage());
        $this->assertStringContainsString($coreException->getMessage(), $exception->getMessage());
    }

    public static function coreExceptionProvider(): array
    {
        return [
            'base table missing' => [
                new ExtraPropertyRegistryException('The base table "ps_demo" does not exist.', ExtraPropertyRegistryException::BASE_TABLE_NOT_FOUND),
                ExtraPropertyRegistrationFailureException::BASE_TABLE_MISSING,
            ],
            'scope conflict' => [
                new ExtraPropertyRegistryException('already registered with scope "common"', ExtraPropertyRegistryException::SCOPE_CONFLICT),
                ExtraPropertyRegistrationFailureException::SCOPE_CONFLICT,
            ],
            'destructive change' => [
                new ExtraPropertyRegistryException('Refusing destructive schema change', ExtraPropertyRegistryException::DESTRUCTIVE_SCHEMA_CHANGE),
                ExtraPropertyRegistrationFailureException::DESTRUCTIVE_CHANGE,
            ],
            'persistence failure' => [
                new ExtraPropertyRegistryException('Failed to persist the definition row', ExtraPropertyRegistryException::PERSISTENCE_FAILURE),
                ExtraPropertyRegistrationFailureException::PERSISTENCE_FAILURE,
            ],
            'schema failure' => [
                new ExtraPropertyRegistryException('Failed to create or alter extra table/column', ExtraPropertyRegistryException::SCHEMA_FAILURE),
                ExtraPropertyRegistrationFailureException::SCHEMA_FAILURE,
            ],
            'invalid form options' => [
                new ExtraPropertyRegistryException('Invalid extra property form options: Unknown option "not_a_real_option"', ExtraPropertyRegistryException::INVALID_FORM_OPTIONS),
                ExtraPropertyRegistrationFailureException::INVALID_FORM_OPTIONS,
            ],
            'registry exception without a known code' => [
                new ExtraPropertyRegistryException('something else', 999),
                ExtraPropertyRegistrationFailureException::UNKNOWN,
            ],
            'non-registry core exception' => [
                new ExtraPropertyException('something else'),
                ExtraPropertyRegistrationFailureException::UNKNOWN,
            ],
        ];
    }
}
