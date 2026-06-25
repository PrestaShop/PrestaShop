<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\ConstraintValidator;

use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\NotCustomizableProduct;
use PrestaShop\PrestaShop\Core\Domain\Product\ProductCustomizabilitySettings;
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

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof NotCustomizableProduct) {
            throw new UnexpectedTypeException($constraint, NotCustomizableProduct::class);
        }

        foreach ($value as $product) {
            $dbProduct = $this->productRepository->getProductByDefaultShop(new ProductId($product['product_id'] ?? $product['id']));
            $isCustomizable = $constraint->requiredOnly
                ? (int) $dbProduct->customizable === ProductCustomizabilitySettings::REQUIRES_CUSTOMIZATION
                : (int) $dbProduct->customizable !== ProductCustomizabilitySettings::NOT_CUSTOMIZABLE;
            if ($isCustomizable) {
                $this->context->buildViolation($constraint->message)
                    ->setTranslationDomain('Admin.Notifications.Error')
                    ->addViolation();
            }
        }
    }
}
