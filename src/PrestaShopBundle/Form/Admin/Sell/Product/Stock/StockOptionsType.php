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

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Product\Stock;

use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\Type;

class StockOptionsType extends TranslatorAwareType
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param RouterInterface $router
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        RouterInterface $router
    ) {
        parent::__construct($translator, $locales);
        $this->router = $router;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('stock_location', TextType::class, [
                'label' => $this->trans('Stock location', 'Admin.Catalog.Feature'),
                'required' => false,
            ])
            ->add('low_stock_threshold', NumberType::class, [
                'label' => $this->trans('Low stock level', 'Admin.Catalog.Feature'),
                'help' => $this->trans('Leave empty to disable', 'Admin.Catalog.Help'),
                'constraints' => [
                    new Type(['type' => 'numeric']),
                ],
                'required' => false,
                'default_empty_data' => 0,
                // Using null here allows to keep the field empty in the page instead of 0
                'empty_view_data' => null,
            ])
            ->add('low_stock_alert', SwitchType::class, [
                'required' => false,
                'label' => $this->trans(
                    'Send me an email when the quantity is below or equals this level',
                    'Admin.Catalog.Feature'
                ),
                'label_help_box' => $this->trans(
                    'The email will be sent to all the users who have the right to run the stock page. To modify the permissions, go to [1]Advanced Parameters > Team[/1]',
                    'Admin.Catalog.Help',
                    [
                        '[1]' => sprintf(
                            '<a target="_blank" href="%s">',
                            $this->router->generate('admin_employees_index')
                        ),
                        '[/1]' => '</a>',
                    ]
                ),
            ])
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'required' => false,
            'label' => $this->trans('Stock', 'Admin.Catalog.Feature'),
            'label_tag_name' => 'h2',
        ]);
    }
}
