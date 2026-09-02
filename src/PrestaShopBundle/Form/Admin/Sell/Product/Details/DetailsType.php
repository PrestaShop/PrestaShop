<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Product\Details;

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Sell\Product\Options\ProductAttachmentsType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class DetailsType extends TranslatorAwareType
{
    /**
     * @var bool
     */
    protected $isFeatureEnabled;

    /**
     * @var FormChoiceProviderInterface
     */
    private $productConditionChoiceProvider;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param FormChoiceProviderInterface $productConditionChoiceProvider
     * @param bool $isFeatureEnabled
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        FormChoiceProviderInterface $productConditionChoiceProvider,
        bool $isFeatureEnabled
    ) {
        parent::__construct($translator, $locales);
        $this->productConditionChoiceProvider = $productConditionChoiceProvider;
        $this->isFeatureEnabled = $isFeatureEnabled;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('references', ReferencesType::class);

        if ($this->isFeatureEnabled) {
            $builder->add('features', FeaturesType::class);
        }

        $builder
            ->add('attachments', ProductAttachmentsType::class)
            ->add('show_condition', SwitchType::class, [
                'required' => false,
                'label' => $this->trans('Display condition on product page', 'Admin.Catalog.Feature'),
                'label_tag_name' => 'h3',
                'show_choices' => false,
                'inline_switch' => true,
                'modify_all_shops' => true,
            ])
            ->add('condition', ChoiceType::class, [
                'choices' => $this->productConditionChoiceProvider->getChoices(),
                'attr' => [
                    'class' => 'custom-select',
                ],
                'required' => false,
                // placeholder false is important to avoid empty option in select input despite required being false
                'placeholder' => false,
                'label' => false,
                'modify_all_shops' => true,
            ])
            ->add('customizations', CustomizationsType::class)
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver
            ->setDefaults([
                'required' => false,
                'label' => $this->trans('Details', 'Admin.Catalog.Feature'),
            ])
        ;
    }
}
