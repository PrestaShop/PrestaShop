<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\ConstraintValidator;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DiscountProductSegment;
use PrestaShopBundle\Form\Admin\Sell\Discount\DiscountProductSegmentType;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Contracts\Translation\TranslatorInterface;

class DiscountProductSegmentValidator extends ConstraintValidator
{
    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    public function validate(mixed $value, Constraint $constraint)
    {
        if (!$constraint instanceof DiscountProductSegment) {
            throw new UnexpectedTypeException($constraint, DiscountProductSegment::class);
        }

        $manufacturer = $value[DiscountProductSegmentType::MANUFACTURER] ?? null;
        $category = $value[DiscountProductSegmentType::CATEGORY] ?? null;
        $supplier = $value[DiscountProductSegmentType::SUPPLIER] ?? null;
        $attributes = $value[DiscountProductSegmentType::ATTRIBUTES]['groups'] ?? null;
        $features = $value[DiscountProductSegmentType::FEATURES]['groups'] ?? null;

        if (empty($manufacturer) && empty($category) && empty($supplier) && empty($attributes) && empty($features)) {
            $this->context->buildViolation($this->translator->trans('At least one product segment must be selected.', [], 'Admin.Notifications.Error'))
                ->addViolation();
        }
    }
}
