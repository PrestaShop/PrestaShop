<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Sell\Discount;

use PrestaShop\PrestaShop\Core\Domain\Supplier\ValueObject\NoSupplierId;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class DiscountSupplierType extends AbstractType
{
    /**
     * @param TranslatorInterface $translator
     * @param FormChoiceProviderInterface $supplierNameByIdChoiceProvider
     */
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly FormChoiceProviderInterface $supplierNameByIdChoiceProvider
    ) {
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $supplier = $this->supplierNameByIdChoiceProvider->getChoices();
        $choices = array_merge([
            $this->trans('No Supplier', 'Admin.Catalog.Feature') => NoSupplierId::NO_SUPPLIER_ID,
        ], $supplier);

        $resolver->setDefaults([
            'label' => $this->trans('Supplier', 'Admin.Catalog.Feature'),
            'label_tag_name' => 'h3',
            'required' => false,
            // placeholder false is important to avoid empty option in select input despite required being false
            'placeholder' => false,
            'choices' => $choices,
            'autocomplete' => true,
        ]);
    }

    /**
     * @param string $key
     * @param string $domain
     * @param array $parameters
     *
     * @return string
     */
    protected function trans(string $key, string $domain, array $parameters = []): string
    {
        return $this->translator->trans($key, $parameters, $domain);
    }
}
