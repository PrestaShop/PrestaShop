<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\General;

use PrestaShop\PrestaShop\Adapter\Entity\Order;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Class returning the content of the form in the maintenance page.
 * To be found in Configure > Shop parameters > General > Maintenance
 */
class PreferencesType extends TranslatorAwareType
{
    /**
     * @var bool
     */
    private $isSecure;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $configuration = $this->getConfiguration();
        $isSslEnabled = $configuration->getBoolean('PS_SSL_ENABLED');

        if ($this->isSecure) {
            $builder->add('enable_ssl', SwitchType::class);
        }

        $builder
            ->add('enable_ssl_everywhere', SwitchType::class, array(
                'disabled' => !$isSslEnabled,
            ))
            ->add('enable_token', SwitchType::class)
            ->add('allow_html_iframes', SwitchType::class)
            ->add('use_htmlpurifier', SwitchType::class)
            ->add('price_round_mode', ChoiceType::class, array(
                'choices_as_values' => true,
                'choices'  => array(
                    'Round up away from zero, when it is half way there (recommended)' =>
                        $configuration->get('PS_ROUND_HALF_UP'),
                    'Round down towards zero, when it is half way there' =>
                        $configuration->get('PS_ROUND_HALF_DOWN'),
                    'Round towards the next even value' =>
                        $configuration->get('PS_ROUND_HALF_EVEN'),
                    'Round towards the next odd value' =>
                        $configuration->get('PS_ROUND_HALF_ODD'),
                    'Round up to the nearest value' =>
                        $configuration->get('PS_ROUND_UP'),
                    'Round down to the nearest value' =>
                        $configuration->get('PS_ROUND_DOWN'),
                ),
            ))
            ->add('price_round_type', ChoiceType::class, array(
                'choices_as_values' => true,
                'choices'  => array(
                    'Round on each item' => Order::ROUND_ITEM,
                    'Round on each line' => Order::ROUND_LINE,
                    'Round on the total' => Order::ROUND_TOTAL,
                ),
            ))
            ->add('price_display_precision', IntegerType::class, array(
                'attr' => [
                    'min' => 0,
                ],
            ))
            ->add('display_suppliers', SwitchType::class)
            ->add('display_best_sellers', SwitchType::class)
            ->add('multishop_feature_active', SwitchType::class)
            ->add('shop_activity', ChoiceType::class, array(
                'required' => false,
                'choices_as_values' => true,
                'placeholder' => $this->trans('-- Please choose your main activity --', 'Install'),
                'choices'  => array(
                    'Animals and Pets' => 2,
                    'Art and Culture' => 3,
                    'Babies' => 4,
                    'Beauty and Personal Care' => 5,
                    'Cars' => 6,
                    'Computer Hardware and Software' => 7,
                    'Download' => 8,
                    'Fashion and accessories' => 9,
                    'Flowers, Gifts and Crafts' => 10,
                    'Food and beverage' => 11,
                    'HiFi, Photo and Video' => 12,
                    'Home and Garden' => 13,
                    'Home Appliances' => 14,
                    'Jewelry' => 15,
                    'Lingerie and Adult' => 1,
                    'Mobile and Telecom' => 16,
                    'Services' => 17,
                    'Shoes and accessories' => 18,
                    'Sport and Entertainment' => 19,
                    'Travel' => 20,
                ),
                'choice_translation_domain' => 'Install',
            ))
        ;
    }

    /**
     * Enabled only if the form is accessed using HTTPS protocol.
     * @var bool
     */
    public function setIsSecure($isSecure)
    {
        $this->isSecure = $isSecure;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'translation_domain' => 'Admin.Shopparameters.Feature',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'preferences';
    }
}
