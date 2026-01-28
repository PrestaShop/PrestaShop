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

use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\NotCustomizableProduct;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class NotCustomizableProductValidator extends ConstraintValidator
{
    public function __construct(
        private ProductRepository $productRepository
    ) {
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof NotCustomizableProduct) {
            throw new UnexpectedTypeException($constraint, NotCustomizableProduct::class);
        }

        foreach ($value as $product) {
            $dbProduct = $this->productRepository->getProductByDefaultShop(new ProductId($product['product_id']));
            if ($dbProduct->customizable) {
                $this->context->buildViolation($constraint->message)
                    ->setTranslationDomain('Admin.Notifications.Error')
                    ->addViolation();
            }
        }
    }
}
