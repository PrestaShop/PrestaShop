<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\ConstraintValidator;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\InstalledApiResourceScope;
use PrestaShop\PrestaShop\Core\ConstraintValidator\InstalledApiResourceScopeValidator;
use PrestaShopBundle\ApiPlatform\Scopes\ApiResourceScopes;
use PrestaShopBundle\ApiPlatform\Scopes\ApiResourceScopesExtractorInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class InstalledApiResourceScopeValidatorTest extends ConstraintValidatorTestCase
{
    public function testItDetectsIncorrectConstraintType()
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->validator->validate([], new NotBlank());
    }

    /**
     * @dataProvider getIncorrectTypes
     */
    public function testItDetectsIncorrectValueType($incorrectType)
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->validator->validate($incorrectType, new InstalledApiResourceScope());
    }

    public function getIncorrectTypes(): iterable
    {
        yield 'string value' => ['scope'];
        yield 'integer value' => [1];
        yield 'integer array value' => [[1]];
        yield 'null value' => [null];
        yield 'false value' => [false];
        yield 'true value' => [true];
    }

    /**
     * @dataProvider getCorrectValues
     */
    public function testCorrectValues($correctValue)
    {
        $this->validator->validate($correctValue, new InstalledApiResourceScope());
        $this->assertNoViolation();
    }

    public function getCorrectValues(): iterable
    {
        yield 'empty array' => [[]];
        yield 'array of core scope' => [['core_scope']];
        yield 'array of module scope' => [['module_scope']];
        yield 'array of all scopes' => [['core_scope', 'module_scope']];
    }

    /**
     * @dataProvider getIncorrectValues
     */
    public function testIncorrectValues($incorrectValue, array $invalidScopes)
    {
        $this->validator->validate($incorrectValue, new InstalledApiResourceScope());
        $this->buildViolation((new InstalledApiResourceScope())->message)
            ->setParameter('%scope_names%', implode(',', $invalidScopes))
            ->assertRaised()
        ;
    }

    public function getIncorrectValues(): iterable
    {
        yield 'only invalid value' => [['invalid_scope'], ['invalid_scope']];
        yield 'one valid then one invalid value' => [['core_scope', 'invalid_scope'], ['invalid_scope']];
        yield 'one invalid then one valid value' => [['invalid_scope', 'core_scope'], ['invalid_scope']];
        yield 'two invalid values' => [['invalid_scope', 'unknown_core_scope'], ['invalid_scope', 'unknown_core_scope']];
        yield 'two invalid values one valid' => [['invalid_scope', 'module_scope', 'unknown_core_scope'], ['invalid_scope', 'unknown_core_scope']];
    }

    protected function createValidator()
    {
        $apiResourceScopesExtractor = $this->createMock(ApiResourceScopesExtractorInterface::class);
        $apiResourceScopesExtractor
            ->method('getAllApiResourceScopes')
            ->willReturn([
                ApiResourceScopes::createCoreScopes(['core_scope']),
                ApiResourceScopes::createModuleScopes(['module_scope'], 'api_module'),
            ])
        ;

        return new InstalledApiResourceScopeValidator($apiResourceScopesExtractor);
    }
}
