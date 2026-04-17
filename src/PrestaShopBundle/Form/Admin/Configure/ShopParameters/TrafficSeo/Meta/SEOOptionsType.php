<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\TrafficSeo\Meta;

use PrestaShopBundle\Form\Admin\Type\MultistoreConfigurationType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use PrestaShopBundle\Form\Extension\MultistoreConfigurationTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * SEOOptionsType manages some options for your SEO meta tags (like product title)
 */
class SEOOptionsType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('product_attributes_in_title', SwitchType::class, [
                'label' => $this->trans(
                    'Display attributes in the product meta title',
                    'Admin.Shopparameters.Feature'
                ),
                'help' => $this->trans(
                    'Enable this option if you want to display your product\'s attributes in its meta title.',
                    'Admin.Shopparameters.Help'
                ),
                'multistore_configuration_key' => 'PS_PRODUCT_ATTRIBUTES_IN_TITLE',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     *
     * @see MultistoreConfigurationTypeExtension
     */
    public function getParent(): string
    {
        return MultistoreConfigurationType::class;
    }
}
