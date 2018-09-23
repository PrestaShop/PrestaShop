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

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\TrafficSeo\Meta;

use PrestaShopBundle\Form\Admin\Type\TranslateTextType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class MetaType is responsible for providing form fields for Shop parameters -> Traffic & Seo ->
 * Seo & Urls -> add and edit forms.
 */
class MetaType extends TranslatorAwareType
{
    /**
     * @var array
     */
    private $defaultPageChoices;
    /**
     * @var array
     */
    private $modulePageChoices;

    /**
     * MetaType constructor.
     *
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param array $defaultPageChoices
     * @param array $modulePageChoices
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        array $defaultPageChoices,
        array $modulePageChoices
    ) {
        parent::__construct($translator, $locales);
        $this->defaultPageChoices = $defaultPageChoices;
        $this->modulePageChoices = $modulePageChoices;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('page_name', ChoiceType::class, [
                'choices' => [
                    $this->trans('Default pages', 'Admin.Shopparameters.Feature') => $this->defaultPageChoices,
                    $this->trans('Module pages', 'Admin.Shopparameters.Feature') => $this->modulePageChoices,
                ],
                'choice_translation_domain' => false,
            ])
            ->add('page_title', TranslateTextType::class, [
                'locales' => $this->locales,
                'required' => false,
            ])
            ->add('meta_description', TranslateTextType::class, [
                'locales' => $this->locales,
                'required' => false,
            ])
            ->add('meta_keywords', TextType::class, [
                'required' => false, //todo: language support
            ])
            ->add('url_rewrite', TranslateTextType::class, [
                'locales' => $this->locales,
            ])
        ;
    }
}
