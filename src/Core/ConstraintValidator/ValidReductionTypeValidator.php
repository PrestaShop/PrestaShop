<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\ConstraintValidator;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\ValidReductionType as TypeConstraint;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Reduction;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validates reduction type
 */
final class ValidReductionTypeValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof TypeConstraint) {
            throw new UnexpectedTypeException($constraint, TypeConstraint::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        if (!$this->isAllowedType($value)) {
            $this->buildViolation(
                $constraint->message,
                [
                    '%type%' => $value,
                    '%types%' => implode(', ', [Reduction::TYPE_PERCENTAGE, Reduction::TYPE_AMOUNT]),
                ],
                '[type]'
            );
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
