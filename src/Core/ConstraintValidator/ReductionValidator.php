<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\ConstraintValidator;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\Reduction as ReductionConstraint;
use PrestaShop\PrestaShop\Core\Domain\Exception\DomainConstraintException;
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

        try {
            new Reduction($value['type'], $value['value']);
        } catch (DomainConstraintException $e) {
            $this->buildViolation($constraint, $value, $e->getCode());
        }
    }

    /**
     * Builds violation dependent from exception code
     *
     * @param ReductionConstraint $constraint
     * @param $value
     * @param int $exceptionCode
     */
    private function buildViolation(ReductionConstraint $constraint, $value, $exceptionCode)
    {
        $message = $constraint->invalidAmountValueMessage;
        $params = ['%value%' => $value['value']];

        if (DomainConstraintException::INVALID_REDUCTION_PERCENTAGE === $exceptionCode) {
            $message = $constraint->invalidPercentageValueMessage;
            $params['%max%'] = Reduction::MAX_ALLOWED_PERCENTAGE;
        }

        if (DomainConstraintException::INVALID_REDUCTION_TYPE === $exceptionCode) {
            $message = $constraint->invalidTypeMessage;
            $params = [
                '%type%' => $value['type'],
                '%types%' => Reduction::TYPE_AMOUNT . ', ' . Reduction::TYPE_PERCENTAGE,
            ];
        }

        $this->context->buildViolation($message, $params)
            ->setTranslationDomain('Admin.Notifications.Error')
            ->setParameters($params)
            ->addViolation()
        ;
    }
}
