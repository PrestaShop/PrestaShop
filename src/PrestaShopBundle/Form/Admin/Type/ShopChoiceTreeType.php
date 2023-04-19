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
