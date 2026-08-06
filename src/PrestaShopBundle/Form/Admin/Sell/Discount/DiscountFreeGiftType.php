<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Discount;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\NotCustomizableProduct;
use PrestaShopBundle\Form\Admin\Type\CardType;
use PrestaShopBundle\Form\Admin\Type\FreeGiftProductSearchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class DiscountFreeGiftType extends TranslatorAwareType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('product', FreeGiftProductSearchType::class, [
                'label' => $this->trans('Free product', 'Admin.Catalog.Feature'),
                'label_tag_name' => 'h3',
                'label_subtitle' => $this->trans('When selecting a free product, please ensure it meets the necessary conditions to be offered as a gift.', 'Admin.Catalog.Feature'),
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    new NotCustomizableProduct(['message' => $this->trans('Product with required customization fields cannot be used as a gift.', 'Admin.Catalog.Notification'), 'requiredOnly' => true]),
                ],
            ])
        ;
    }

    public function getParent()
    {
        return CardType::class;
    }
}
