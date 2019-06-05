<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Form\Admin\Improve\Design\MailTheme;

use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class GenerateMailsType is responsible for build the form to generate mail templates.
 */
class GenerateMailsType extends TranslatorAwareType
{
    /** @var array */
    private $mailThemes;

    /** @var array */
    private $themes;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param array $mailThemes
     * @param array $themes
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        array $mailThemes,
        array $themes
    ) {
        parent::__construct($translator, $locales);
        $this->mailThemes = $mailThemes;
        $this->themes = $themes;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $noTheme = $this->trans('Core (no theme selected)', 'Admin.International.Feature');

        $builder
            ->add('mailTheme', ChoiceType::class, [
                'choices' => $this->mailThemes,
                'data' => $this->getConfiguration()->get('PS_MAIL_THEME'),
            ])
            ->add('language', ChoiceType::class, [
                'placeholder' => $this->trans('Language', 'Admin.Global'),
                'choices' => $this->getLocaleChoices(),
                'choice_translation_domain' => false,
            ])
            ->add('theme', ChoiceType::class, [
                'choices' => $this->themes,
                'placeholder' => $noTheme,
                'required' => false,
                'empty_data' => '',
                'data' => '',
                'disabled' => count($this->themes) <= 0,
            ])
            ->add('overwrite', SwitchType::class, ['data' => false])
        ;
    }
}
