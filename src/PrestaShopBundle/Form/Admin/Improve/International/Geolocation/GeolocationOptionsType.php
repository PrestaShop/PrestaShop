<?php
/**
 * 2007-2018 PrestaShop.
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

namespace PrestaShopBundle\Form\Admin\Improve\International\Geolocation;

use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShopBundle\Form\Admin\Type\Material\MaterialChoiceTableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class GeolocationOptionsType is responsible for handling "Improve > International > Localization > Geolocation"
 * geolocation options form.
 */
class GeolocationOptionsType extends TranslatorAwareType
{
    /**
     * @var array
     */
    private $countryChoices;

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param array $countryChoices
     * @param ConfigurationInterface $configuration
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        array $countryChoices,
        ConfigurationInterface $configuration
    ) {
        parent::__construct($translator, $locales);

        $this->countryChoices = $countryChoices;
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('geolocation_behaviour', ChoiceType::class, array(
                'choices' => array(
                    $this->trans('Visitors cannot see your catalog.', 'Admin.International.Feature') => $this->configuration->get('_PS_GEOLOCATION_NO_CATALOG_'),
                    $this->trans('Visitors can see your catalog but cannot place an order.', 'Admin.International.Feature') => $this->configuration->get('_PS_GEOLOCATION_NO_ORDER_'),
                ),
                'choice_translation_domain' => false,
            ))
            ->add('geolocation_na_behaviour', ChoiceType::class, array(
                'choices' => array(
                    $this->trans('All features are available', 'Admin.International.Feature') => '-1',
                    $this->trans('Visitors cannot see your catalog.', 'Admin.International.Feature') => $this->configuration->get('_PS_GEOLOCATION_NO_CATALOG_'),
                    $this->trans('Visitors can see your catalog but cannot place an order.', 'Admin.International.Feature') => $this->configuration->get('_PS_GEOLOCATION_NO_ORDER_'),
                ),
                'choice_translation_domain' => false,
            ))
            ->add('geolocation_countries', MaterialChoiceTableType::class, array(
                'choices' => $this->countryChoices,
                'choice_translation_domain' => false,
            ))
        ;

        $builder->get('geolocation_countries')->addModelTransformer(new CallbackTransformer(
            function ($countriesAsString) {
                return explode(';', $countriesAsString);
            },
            function ($countriesAsArray) {
                return implode(';', $countriesAsArray);
            }
        ));
    }
}
