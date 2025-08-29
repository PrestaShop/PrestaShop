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

        if (empty($manufacturer) && empty($category) && empty($supplier) && empty($attributes)) {
            $this->context->buildViolation($this->translator->trans('At least one product segment must be selected.', [], 'Admin.Notifications.Error'))
                ->addViolation();
        }
    }
}
