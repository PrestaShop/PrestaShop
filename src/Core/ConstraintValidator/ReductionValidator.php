<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\ConstraintValidator;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\Reduction as ReductionConstraint;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Reduction;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validates reduction type and value
 */
final class ReductionValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof ReductionConstraint) {
            throw new UnexpectedTypeException($constraint, ReductionConstraint::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_array($value)) {
            throw new UnexpectedTypeException($value, 'array');
        }

        if (null === $value['value'] || null === $value['type']) {
            // when one of these are null, then we assume the ReductionType was disabled, so we skip the validation
            return;
        }

        if (!$this->isAllowedType($value['type'])) {
            $this->buildViolation(
                $constraint->invalidTypeMessage,
                [
                    '%type%' => $value['type'],
                    '%types%' => implode(', ', [Reduction::TYPE_PERCENTAGE, Reduction::TYPE_AMOUNT]),
                ],
                '[type]'
            );
        }

        if (Reduction::TYPE_AMOUNT === $value['type']) {
            if (!is_numeric($value['value']) || !$this->assertIsValidAmount($value['value'])) {
                $this->buildViolation(
                    $constraint->invalidAmountValueMessage,
                    ['%value%' => $value['value']],
                    '[value]'
                );
            }
        } elseif (Reduction::TYPE_PERCENTAGE === $value['type']) {
            if (!is_numeric($value['value']) || !$this->assertIsValidPercentage($value['value'])) {
                $this->buildViolation(
                    $constraint->invalidPercentageValueMessage,
                    [
                        '%value%' => $value['value'],
                        '%max%' => Reduction::MAX_ALLOWED_PERCENTAGE,
                    ],
                    '[value]'
                );
            }
        }
    }

    /**
     * Returns true if type is defined in allowed types, false otherwise
     *
     * @param string $type
     *
     * @return bool
     */
    private function isAllowedType(string $type): bool
    {
        return in_array($type, Reduction::ALLOWED_TYPES, true);
    }

    /**
     * Returns true is percentage is considered valid
     *
     * @param float $value
     *
     * @return bool
     */
    private function assertIsValidPercentage(float $value)
    {
        return 0 < $value && Reduction::MAX_ALLOWED_PERCENTAGE >= $value;
    }

    /**
     * Returns true if amount value is considered valid
     *
     * @param float $value
     *
     * @return bool
     */
    private function assertIsValidAmount(float $value)
    {
        return 0 < $value;
    }

    /**
     * Builds violation dependent from exception code
     *
     * @param string $message
     * @param array $params
     * @param string $errorPath
     */
    private function buildViolation(string $message, array $params, string $errorPath)
    {
        $this->context->buildViolation($message, $params)
            ->setTranslationDomain('Admin.Notifications.Error')
            ->atPath($errorPath)
            ->setParameters($params)
            ->addViolation()
        ;
    }
}
