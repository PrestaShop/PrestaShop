<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * A constraint validator that does nothing.
 *
 * Returned by GracefulConstraintValidatorFactory when a constraint's real validator cannot be built in the current
 * (front-office legacy) container — so an un-buildable constraint is skipped rather than fataling. The constraint is
 * still fully enforced wherever the full Symfony container runs (back-office Symfony pages and the Admin API).
 */
final class NoOpConstraintValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint)
    {
        // Intentionally a no-op.
    }
}
