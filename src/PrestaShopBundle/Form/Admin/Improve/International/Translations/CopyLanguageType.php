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

namespace PrestaShopBundle\Form\Admin\Improve\International\Translations;

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class CopyLanguageType is responsible for building 'Copy' form
 * in 'Improve > International > Translations' page
 */
class CopyLanguageType extends TranslatorAwareType
{
    /**
     * @var FormChoiceProviderInterface
     */
    private $themeChoiceProvider;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param FormChoiceProviderInterface $themeChoiceProvider
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        FormChoiceProviderInterface $themeChoiceProvider
    ) {
        parent::__construct($translator, $locales);

        $this->locales = $locales;
        $this->themeChoiceProvider = $themeChoiceProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $themeChoices = $this->themeChoiceProvider->getChoices();

        $builder
            ->add('from_language', ChoiceType::class, [
                'label' => $this->trans('From', 'Admin.Global'),
                'choices' => $this->getLocaleChoices(),
            ])
            ->add('from_theme', ChoiceType::class, [
                'choices' => $themeChoices,
            ])
            ->add('to_language', ChoiceType::class, [
                'choices' => $this->getLocaleChoices(),
            ])
            ->add('to_theme', ChoiceType::class, [
                'choices' => $themeChoices,
            ])
        ;
    }
}
