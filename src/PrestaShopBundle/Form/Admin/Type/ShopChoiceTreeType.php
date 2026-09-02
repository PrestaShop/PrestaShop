<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Type;

use PrestaShop\PrestaShop\Core\Feature\FeatureInterface;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShop\PrestaShop\Core\Shop\ShopContextInterface;
use PrestaShopBundle\Form\Admin\Type\Material\MaterialChoiceTreeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ShopChoiceTreeType.
 */
class ShopChoiceTreeType extends AbstractType
{
    /**
     * @var FormChoiceProviderInterface
     */
    private $shopTreeChoiceProvider;

    /**
     * @var DataTransformerInterface
     */
    private $stringArrayToIntegerArrayDataTransformer;

    /**
     * @var ShopContextInterface
     */
    private $shopContext;

    /**
     * @var FeatureInterface
     */
    private $multiStoreFeature;

    /**
     * @param FormChoiceProviderInterface $shopTreeChoiceProvider
     * @param DataTransformerInterface $stringArrayToIntegerArrayDataTransformer
     * @param ShopContextInterface $shopContext
     * @param FeatureInterface $multiStoreFeature
     */
    public function __construct(
        FormChoiceProviderInterface $shopTreeChoiceProvider,
        DataTransformerInterface $stringArrayToIntegerArrayDataTransformer,
        ShopContextInterface $shopContext,
        FeatureInterface $multiStoreFeature
    ) {
        $this->shopTreeChoiceProvider = $shopTreeChoiceProvider;
        $this->stringArrayToIntegerArrayDataTransformer = $stringArrayToIntegerArrayDataTransformer;
        $this->shopContext = $shopContext;
        $this->multiStoreFeature = $multiStoreFeature;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->stringArrayToIntegerArrayDataTransformer);

        parent::buildForm($builder, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'choices_tree' => $this->shopTreeChoiceProvider->getChoices(),
            'multiple' => true,
            'choice_label' => 'name',
            'choice_value' => 'id_shop',
            'default_empty_data' => $this->shopContext->getContextShopIds(),
            'form_theme' => '@PrestaShop/Admin/TwigTemplateForm/prestashop_ui_kit.html.twig',
            'row_attr' => [
                'class' => $this->multiStoreFeature->isUsed() ? '' : 'd-none',
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return MaterialChoiceTreeType::class;
    }
}
