<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
            if (!$this->assertIsValidAmount($value['value'])) {
                $this->buildViolation(
                    $constraint->invalidAmountValueMessage,
                    ['%value%' => $value['value']],
                    '[value]'
                );
            }
        } elseif (Reduction::TYPE_PERCENTAGE === $value['type']) {
            if (!$this->assertIsValidPercentage($value['value'])) {
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
        return 0 <= $value && Reduction::MAX_ALLOWED_PERCENTAGE >= $value;
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
        return 0 <= $value;
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
