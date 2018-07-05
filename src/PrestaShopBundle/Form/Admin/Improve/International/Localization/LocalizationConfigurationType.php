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

namespace PrestaShopBundle\Form\Admin\Improve\International\Localization;

use PrestaShopBundle\Form\Admin\Type\SwitchType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class LocalizationConfigurationType is responsible for building 'Improve > International > Localization' page
 * 'Configuration' form.
 */
class LocalizationConfigurationType extends AbstractType
{
    /**
     * @var array
     */
    private $languageChoices;

    /**
     * @var array
     */
    private $countryChoices;

    /**
     * @var array
     */
    private $currencyChoices;

    /**
     * @var array
     */
    private $timezoneChoices;

    /**
     * @param array $languageChoices
     * @param array $countryChoices
     * @param array $currencyChoices
     * @param array $timezoneChoices
     */
    public function __construct(
        array $languageChoices,
        array $countryChoices,
        array $currencyChoices,
        array $timezoneChoices
    ) {
        $this->languageChoices = $languageChoices;
        $this->countryChoices = $countryChoices;
        $this->currencyChoices = $currencyChoices;
        $this->timezoneChoices = $timezoneChoices;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('default_language', ChoiceType::class, [
                'choices' => $this->languageChoices,
            ])
            ->add('detect_language_from_browser', SwitchType::class)
            ->add('default_country', ChoiceType::class, [
                'choices' => $this->countryChoices,
            ])
            ->add('detect_country_from_browser', SwitchType::class)
            ->add('default_currency', ChoiceType::class, [
                'choices' => $this->currencyChoices,
            ])
            ->add('timezone', ChoiceType::class, [
                'choices' => $this->timezoneChoices,
            ])
        ;
    }
}
