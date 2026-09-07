<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\ExtraProperty\Validation;

use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyType;
use PrestaShop\PrestaShop\Core\ExtraProperty\Validation\ExtraPropertyTypeCompatibility;
use PrestaShop\PrestaShop\Core\ExtraProperty\Validation\ExtraPropertyTypeCompatibilityValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * The constraint form of ExtraPropertyValidator::isValueCompatible() — the rule matrix
 * itself is pinned by ExtraPropertyValidatorTest::testIsValueCompatible(); this covers
 * the Symfony wiring (violation, message parameter, enum carried by the constraint).
 */
class ExtraPropertyTypeCompatibilityValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator(): ExtraPropertyTypeCompatibilityValidator
    {
        return new ExtraPropertyTypeCompatibilityValidator();
    }

    public function testCompatibleValueRaisesNothing(): void
    {
        $this->validator->validate('42', new ExtraPropertyTypeCompatibility(ExtraPropertyType::INT));

        $this->assertNoViolation();
    }

    public function testIncompatibleValueIsReportedWithTheTypeParameter(): void
    {
        $constraint = new ExtraPropertyTypeCompatibility(ExtraPropertyType::DATE);

        $this->validator->validate('tomorrow', $constraint);

        $this->buildViolation($constraint->message)
            ->setParameter('{{ type }}', 'date')
            ->assertRaised();
    }

    public function testChoiceEnumValuesAreCarriedByTheConstraint(): void
    {
        $constraint = new ExtraPropertyTypeCompatibility(ExtraPropertyType::CHOICE, ['a', 'b']);

        $this->validator->validate('z', $constraint);

        $this->buildViolation($constraint->message)
            ->setParameter('{{ type }}', 'choice')
            ->assertRaised();
    }
}
