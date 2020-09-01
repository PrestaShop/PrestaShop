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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ShopUrlType is responsible for providing form fields for
 * Shop parameters -> Traffic & Seo -> Seo & Urls -> Shop urls block.
 */
class ShopUrlType extends AbstractType
{
    /**
     * @var bool
     */
    private $isHostMode;

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
     * @param bool $isHostMode
     * @param bool $isShopFeatureActive
     * @param bool $doesMainShopUrlExist
     */
    public function __construct($isHostMode, $isShopFeatureActive, $doesMainShopUrlExist)
    {
        $this->isHostMode = $isHostMode;
        $this->isShopFeatureActive = $isShopFeatureActive;
        $this->doesMainShopUrlExist = $doesMainShopUrlExist;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!$this->isHostMode && !$this->isShopFeatureActive && $this->doesMainShopUrlExist) {
            $builder
                ->add('domain', TextType::class)
                ->add('domain_ssl', TextType::class)
                ->add('physical_uri', TextType::class);
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
