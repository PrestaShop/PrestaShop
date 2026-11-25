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
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;

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
            ->add('noindex_url_parameters_list', TextType::class, [
                'label' => $this->trans(
                    'URL parameters not to index',
                    'Admin.Shopparameters.Feature'
                ),
                'help' => $this->trans(
                    'Comma-separated list of URL parameter names, for example: back,page. Search engines are asked not to index (noindex) the pages whose URL contains one of them.',
                    'Admin.Shopparameters.Help'
                ),
                'required' => false,
                'empty_data' => '',
                'constraints' => [
                    new Regex([
                        'pattern' => '/^\s*([\w-]+(\s*,\s*[\w-]+)*)?\s*$/',
                        'message' => $this->trans(
                            'This field must be a comma-separated list of names made of letters, numbers, underscores (_) and hyphens (-).',
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
                'multistore_configuration_key' => 'PS_NOINDEX_URL_PARAMETERS_LIST',
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
