<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Product\Combination;

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class CombinationAvailabilityType extends TranslatorAwareType
{
    /**
     * @var FormChoiceProviderInterface
     */
    private $outOfStockTypeChoiceProvider;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param FormChoiceProviderInterface $outOfStockTypeChoiceProvider
     * @param RouterInterface $router
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        FormChoiceProviderInterface $outOfStockTypeChoiceProvider,
        RouterInterface $router
    ) {
        parent::__construct($translator, $locales);
        $this->outOfStockTypeChoiceProvider = $outOfStockTypeChoiceProvider;
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('out_of_stock_type', ChoiceType::class, [
                'choices' => $this->outOfStockTypeChoiceProvider->getChoices(),
                'label' => false,
                'expanded' => true,
                'column_breaker' => true,
                'modify_all_shops' => true,
                'external_link' => [
                    'text' => $this->trans('[1]Edit default behavior[/1]', 'Admin.Catalog.Feature'),
                    'href' => $this->router->generate('admin_product_preferences') . '#configuration_fieldset_stock',
                ],
            ])
            ->add('available_now_label', TranslatableType::class, [
                'type' => TextType::class,
                'label' => $this->trans('Label when in stock', 'Admin.Catalog.Feature'),
                'required' => false,
                'modify_all_shops' => true,
                'help' => $this->trans('This will be the displayed availability of the product, if there is at least 1 in stock. If you don\'t enter anything, value from [1]Shop Parameters > Product Settings[/1] will be used.',
                    'Admin.Catalog.Help',
                    [
                        '[1]' => '<a href="' . $this->router->generate('admin_product_preferences') . '#configuration_fieldset_stock">',
                        '[/1]' => '</a>',
                    ]
                ),
            ])
            ->add('available_later_label', TranslatableType::class, [
                'type' => TextType::class,
                'label' => $this->trans('Label when out of stock', 'Admin.Catalog.Feature'),
                'required' => false,
                'modify_all_shops' => true,
                'help' => $this->trans('This will be the displayed availability of the product, if it\'s not in stock. If you don\'t enter anything, value from [1]Shop Parameters > Product Settings[/1] will be used.',
                    'Admin.Catalog.Help',
                    [
                        '[1]' => '<a href="' . $this->router->generate('admin_product_preferences') . '#configuration_fieldset_stock">',
                        '[/1]' => '</a>',
                    ]
                ),
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'label' => $this->trans('When out of stock', 'Admin.Catalog.Feature'),
                'label_tag_name' => 'h3',
                'required' => false,
                'columns_number' => 3,
                'row_attr' => [
                    'class' => 'combination-availability',
                ],
            ])
        ;
    }
}
