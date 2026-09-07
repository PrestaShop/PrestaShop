<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Validation;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Dependency-free on purpose: instantiable by every validator, including the hand-built
 * front-office one (ValidatorBuilderExtension) and Validation::createValidator() in tests.
 */
final class ExtraPropertyTypeCompatibilityValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ExtraPropertyTypeCompatibility) {
            throw new UnexpectedTypeException($constraint, ExtraPropertyTypeCompatibility::class);
        }

        if (ExtraPropertyValidator::isValueCompatible($constraint->type, $value, $constraint->enumValues)) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ type }}', $constraint->type->value)
            ->addViolation();
    }
}
