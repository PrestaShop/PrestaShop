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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\TrafficSeo\Meta;

use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ShopUrlType is responsible for providing form fields for
 * Shop parameters -> Traffic & Seo -> Seo & Urls -> Shop urls block.
 */
class ShopUrlType extends TranslatorAwareType
{
    /**
     * @var bool
     */
    private $isShopFeatureActive;

    /**
     * @var bool
     */
    private $doesMainShopUrlExist;

    /**
     * ShopUrlType constructor.
     *
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param bool $isShopFeatureActive
     * @param bool $doesMainShopUrlExist
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        bool $isShopFeatureActive,
        bool $doesMainShopUrlExist
    ) {
        parent::__construct($translator, $locales);
        $this->isShopFeatureActive = $isShopFeatureActive;
        $this->doesMainShopUrlExist = $doesMainShopUrlExist;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!$this->isShopFeatureActive && $this->doesMainShopUrlExist) {
            $builder
                ->add('domain', TextType::class, [
                    'label' => $this->trans(
                        'Shop domain',
                        'Admin.Shopparameters.Feature'
                    ),
                ])
                ->add('domain_ssl', TextType::class, [
                    'label' => $this->trans(
                        'SSL domain',
                        'Admin.Shopparameters.Feature'
                    ),
                ])
                ->add('physical_uri', TextType::class, [
                    'label' => $this->trans(
                        'Base URI',
                        'Admin.Shopparameters.Feature'
                    ),
                ]);
        }
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
}
