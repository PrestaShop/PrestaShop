<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\ConstraintValidator;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DiscountAmount;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Reduction;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validates discount amount with the nested value structure:
 * ['type' => ..., 'value' => ['amount' => ..., 'currency' => ...], 'include_tax' => ...]
 */
class DiscountAmountValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof DiscountAmount) {
            throw new UnexpectedTypeException($constraint, DiscountAmount::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_array($value)) {
            throw new UnexpectedTypeException($value, 'array');
        }

        $type = $value['type'] ?? null;
        $amount = $value['value']['amount'] ?? null;

        if (null === $amount || null === $type) {
            return;
        }

        if (!in_array($type, Reduction::ALLOWED_TYPES, true)) {
            $this->context->buildViolation($constraint->invalidTypeMessage, [
                '%type%' => $type,
                '%types%' => implode(', ', Reduction::ALLOWED_TYPES),
            ])
                ->setTranslationDomain('Admin.Notifications.Error')
                ->atPath('[type]')
                ->addViolation()
            ;
        }

        if (Reduction::TYPE_AMOUNT === $type) {
            if (!is_numeric($amount) || (float) $amount <= 0) {
                $this->context->buildViolation($constraint->invalidAmountValueMessage, [
                    '%value%' => $amount,
                ])
                    ->setTranslationDomain('Admin.Notifications.Error')
                    ->atPath('[value][amount]')
                    ->addViolation()
                ;
            }
        } elseif (Reduction::TYPE_PERCENTAGE === $type) {
            if (!is_numeric($amount) || (float) $amount <= 0 || (float) $amount > Reduction::MAX_ALLOWED_PERCENTAGE) {
                $this->context->buildViolation($constraint->invalidPercentageValueMessage, [
                    '%value%' => $amount,
                    '%max%' => Reduction::MAX_ALLOWED_PERCENTAGE,
                ])
                    ->setTranslationDomain('Admin.Notifications.Error')
                    ->atPath('[value][amount]')
                    ->addViolation()
                ;
            }
        }
    }
}
