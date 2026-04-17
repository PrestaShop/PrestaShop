<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
