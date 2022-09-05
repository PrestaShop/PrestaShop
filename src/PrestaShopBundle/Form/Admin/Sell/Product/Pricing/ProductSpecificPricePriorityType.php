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

use PrestaShop\PrestaShop\Adapter\Product\SpecificPrice\Repository\SpecificPriceRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\ValueObject\PriorityList;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

class ProductSpecificPricePriorityType extends TranslatorAwareType
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var SpecificPriceRepository
     */
    private $specificPriceRepository;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param RouterInterface $router
     * @param SpecificPriceRepository $specificPriceRepository
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        RouterInterface $router,
        SpecificPriceRepository $specificPriceRepository
    ) {
        parent::__construct($translator, $locales);
        $this->router = $router;
        $this->specificPriceRepository = $specificPriceRepository;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array<string, mixed> $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('use_custom_priority', ChoiceType::class, [
                'choices' => [
                    $this->buildDefaultPriorityChoiceLabel() => false,
                    $this->trans('Set a specific order for this product', 'Admin.Catalog.Feature') => true,
                ],
                'default_empty_data' => false,
                'placeholder' => false,
                'expanded' => true,
                'multiple' => false,
                'required' => false,
                'label' => false,
                'external_link' => [
                    'text' => $this->trans('[1]Manage default settings[/1]', 'Admin.Actions'),
                    'href' => $this->router->generate('admin_product_preferences'),
                    'position' => 'prepend',
                ],
            ])
            ->add('priorities', SpecificPricePriorityType::class, [
                'label' => false,
                'row_attr' => [
                    'class' => 'specific-price-priority-list',
                ],
            ])
        ;
    }

    /**
     * @return string
     */
    private function buildDefaultPriorityChoiceLabel(): string
    {
        $defaultPriorities = implode(' - ', $this->getTranslatedDefaultPriorities());

        return $this->trans(
            'Use default order: [1]{priority_list}[/1]',
            'Admin.Catalog.Feature',
            ['[1]' => '<strong>&nbsp', '[/1]' => '</strong>', '{priority_list}' => $defaultPriorities]
        );
    }

    /**
     * @return string[]
     */
    private function getTranslatedDefaultPriorities(): array
    {
        $priorityList = $this->specificPriceRepository->getDefaultPriorities();

        $priorityTranslations = [
            PriorityList::PRIORITY_SHOP => $this->trans('Store', 'Admin.Global'),
            PriorityList::PRIORITY_CURRENCY => $this->trans('Currency', 'Admin.Global'),
            PriorityList::PRIORITY_COUNTRY => $this->trans('Country', 'Admin.Global'),
            PriorityList::PRIORITY_GROUP => $this->trans('Group', 'Admin.Global'),
        ];

        $translatedPriorities = [];
        foreach ($priorityList->getPriorities() as $priority) {
            $translatedPriorities[] = $priorityTranslations[$priority];
        }

        return $translatedPriorities;
    }
}
