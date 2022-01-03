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

namespace PrestaShopBundle\Form\Admin\Sell\Product\Pricing;

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

class SpecificPricePriorityType extends TranslatorAwareType
{
    /**
     * @var FormChoiceProviderInterface
     */
    private $priorityTypeChoiceProvider;

    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        FormChoiceProviderInterface $priorityTypeChoiceProvider,
        RouterInterface $router
    ) {
        parent::__construct($translator, $locales);
        $this->priorityTypeChoiceProvider = $priorityTypeChoiceProvider;
        $this->priorityTypeChoiceProvider = $priorityTypeChoiceProvider;
        $this->router = $router;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array<string, mixed> $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('priority_type', ChoiceType::class, [
                'choices' => $this->priorityTypeChoiceProvider->getChoices(),
                'default_empty_data' => false,
                'placeholder' => false,
                'expanded' => true,
                'multiple' => false,
                'required' => false,
                'label' => false,
                'external_link' => [
                    'text' => $this->trans('[1]Manage default settings[/1]', 'Admin.Global'),
                    'href' => $this->router->generate('admin_product_preferences'),
                ],
            ])
            ->add('priorities', PriorityListType::class, [
                'label' => false,
                'row_attr' => [
                    // hide by default. Javascript handles visibility based on priority type choice
                    'class' => 'specific-price-priority-list d-none',
                ],
            ])
        ;
    }
}
