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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ConstraintValidator;

use PrestaShop\PrestaShop\Adapter\Discount\Repository\DiscountRepository;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\UniqueDiscountCode;
use Symfony\Component\Form\Form;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validation constraint for making sure that a discount code isn't already used by another discount.
 */
class UniqueDiscountCodeValidator extends ConstraintValidator
{
    public function __construct(
        protected readonly DiscountRepository $discountRepository
    ) {
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueDiscountCode) {
            throw new UnexpectedTypeException($constraint, UniqueDiscountCode::class);
        }

        // If the discount code is empty, no need to check for uniqueness
        if (empty($value)) {
            return;
        }

        // If the discount code is not empty, check if it already exists
        /** @var Form $form */
        $form = $this->context->getObject();
        $formData = $form->getRoot()->getNormData();

        $existingDiscountId = $this->discountRepository->getIdByCode($value);

        // If we don't have discount with this code, or if the existing discount
        // is the same as the one being edited => no violation
        if (null === $existingDiscountId || isset($formData['id']) && $existingDiscountId === $formData['id']) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->setTranslationDomain('Admin.Notifications.Error')
            ->setParameter('%s', $this->formatValue($existingDiscountId))
            ->addViolation();
    }
}
