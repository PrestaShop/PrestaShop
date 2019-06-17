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

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\ReductionByType;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Reduction;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validates reduction value according to provided reduction type by its property path
 */
final class ReductionByTypeValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof ReductionByType) {
            throw new UnexpectedTypeException($constraint, ReductionByType::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_numeric($value)) {
            throw new UnexpectedTypeException($value, 'numeric');
        }

        if (!$this->isReductionValid($constraint, $value)) {
            $this->context->buildViolation($constraint->message)
                ->setTranslationDomain('Admin.Notifications.Error')
                ->setParameter('%s', $this->formatValue($value))
                ->addViolation()
            ;
        }
    }

    /**
     * @param ReductionByType $constraint
     * @param float $value
     *
     * @return bool
     */
    private function isReductionValid($constraint, $value)
    {
        $path = $constraint->reductionTypePath;

        try {
            $reductionType = PropertyAccess::createPropertyAccessor()->getValue($this->context->getObject(), $path);

            if (Reduction::TYPE_PERCENTAGE === $reductionType) {
                return Reduction::MAX_ALLOWED_PERCENTAGE >= $value;
            }
        } catch (NoSuchPropertyException $e) {
            throw new ConstraintDefinitionException(sprintf(
                'Invalid property path "%s" provided to "%s" constraint: %s',
                $path,
                \get_class($constraint),
                $e->getMessage()),
                0,
                $e
            );
        }

        return true;
    }
}
