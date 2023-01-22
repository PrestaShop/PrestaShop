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

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\TrafficSeo\Meta;

use PrestaShop\PrestaShop\Adapter\Routes\DefaultRouteProvider;
use PrestaShopBundle\Form\Admin\Type\MultistoreConfigurationType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class UrlSchemaType is responsible for providing form fields for
 * Shop parameters -> Traffic & Seo -> Seo & Urls -> Schema of urls block.
 */
class UrlSchemaType extends TranslatorAwareType
{
    /**
     * @var DefaultRouteProvider
     */
    private $defaultRouteProvider;

    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        DefaultRouteProvider $defaultRouteProvider
    ) {
        parent::__construct($translator, $locales);
        $this->defaultRouteProvider = $defaultRouteProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('product_rule', TextType::class, [
                'label' => $this->trans(
                    'Route to products',
                    'Admin.Shopparameters.Feature'
                ),
                'help' => $this->getKeywords('product_rule'),
                'multistore_configuration_key' => 'PS_ROUTE_product_rule',
            ])
            ->add('category_rule', TextType::class, [
                'label' => $this->trans(
                    'Route to category',
                    'Admin.Shopparameters.Feature'
                ),
                'help' => $this->getKeywords('category_rule'),
                'multistore_configuration_key' => 'PS_ROUTE_category_rule',
            ])
            ->add('supplier_rule', TextType::class, [
                'label' => $this->trans(
                    'Route to supplier',
                    'Admin.Shopparameters.Feature'
                ),
                'help' => $this->getKeywords('supplier_rule'),
                'multistore_configuration_key' => 'PS_ROUTE_supplier_rule',
            ])
            ->add('manufacturer_rule', TextType::class, [
                'label' => $this->trans(
                    'Route to brand',
                    'Admin.Shopparameters.Feature'
                ),
                'help' => $this->getKeywords('manufacturer_rule'),
                'multistore_configuration_key' => 'PS_ROUTE_manufacturer_rule',
            ])
            ->add('cms_rule', TextType::class, [
                'label' => $this->trans(
                    'Route to page',
                    'Admin.Shopparameters.Feature'
                ),
                'help' => $this->getKeywords('cms_rule'),
                'multistore_configuration_key' => 'PS_ROUTE_cms_rule',
            ])
            ->add('cms_category_rule', TextType::class, [
                'label' => $this->trans(
                    'Route to page category',
                    'Admin.Shopparameters.Feature'
                ),
                'help' => $this->getKeywords('cms_category_rule'),
                'multistore_configuration_key' => 'PS_ROUTE_cms_category_rule',
            ])
            ->add('module', TextType::class, [
                'label' => $this->trans(
                    'Route to modules',
                    'Admin.Shopparameters.Feature'
                ),
                'help' => $this->getKeywords('module'),
                'multistore_configuration_key' => 'PS_ROUTE_module',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => false,
        ]);
    }

    /**
     * @param string $idRoute
     *
     * @return string
     *
     * @throws \PrestaShopException
     */
    private function getKeywords($idRoute)
    {
        $keyWords = $this->defaultRouteProvider->getKeywords();
        $formattedKeyWords = [];
        if ($keyWords[$idRoute]) {
            foreach ($keyWords[$idRoute] as $key => $keyWord) {
                $value = $key;
                if (isset($keyWord['param'])) {
                    $value .= '*';
                }
                $formattedKeyWords[] = $value;
            }
        }

        return $this->trans(
                'Keywords: %keywords%',
                'Admin.Shopparameters.Feature',
                [
                    '%keywords%' => implode(', ', $formattedKeyWords),
                ]
        );
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
