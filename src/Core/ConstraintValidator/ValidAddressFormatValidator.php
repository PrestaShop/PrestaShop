<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ConstraintValidator;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\ValidAddressFormat;
use PrestaShop\PrestaShop\Core\Domain\Country\AddressFormat\AddressFormatCheckerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class ValidAddressFormatValidator extends ConstraintValidator
{
    public function __construct(private readonly AddressFormatCheckerInterface $checker)
    {
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof ValidAddressFormat) {
            throw new UnexpectedTypeException($constraint, ValidAddressFormat::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        $errors = $this->checker->validate($value);
        foreach ($errors as $error) {
            $this->context->buildViolation($error)->addViolation();
        }
    }
}
