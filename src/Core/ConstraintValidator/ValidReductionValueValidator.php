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

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\ValidReductionValue as ValueConstraint;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Reduction;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;


/**
 * Validates reduction value
 */
final class ValidReductionValueValidator extends ConstraintValidator
{
    private $propertyAccessor;

    public function __construct(PropertyAccessorInterface $propertyAccessor = null)
    {
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof ValueConstraint) {
            throw new UnexpectedTypeException($constraint, ValueConstraint::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_numeric($value)) {
            throw new UnexpectedTypeException($value, 'numeric');
        }

        if ($path = $constraint->propertyPath) {
            if (null === $object = $this->context->getObject()) {
                return;
            }
            try {
                $reductionType = $this->getPropertyAccessor()->getValue($object, $path);
            } catch (NoSuchPropertyException $e) {
                throw new ConstraintDefinitionException(sprintf('Invalid property path "%s" provided to "%s" constraint: %s', $path, \get_class($constraint), $e->getMessage()), 0, $e);
            }
        } else {
            $reductionType = $constraint->reductionType;
        }

        if (Reduction::TYPE_AMOUNT === $reductionType) {
            if (!$this->assertIsValidAmount($value)) {
                $this->buildViolation(
                    $constraint->invalidAmountValueMessage,
                    ['%value%' => $value],
                    '[value]'
                );
            }
        } elseif (Reduction::TYPE_PERCENTAGE === $reductionType) {
            if (!$this->assertIsValidPercentage($value)) {
                $this->buildViolation(
                    $constraint->invalidPercentageValueMessage,
                    [
                        '%value%' => $value,
                        '%max%' => Reduction::MAX_ALLOWED_PERCENTAGE,
                    ],
                    '[value]'
                );
            }
        }
    }

    /**
     * Get PropertyAccessor service
     *
     * @return PropertyAccessorInterface
     */
    private function getPropertyAccessor()
    {
        if (null === $this->propertyAccessor) {
            $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
        }

        return $this->propertyAccessor;
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
