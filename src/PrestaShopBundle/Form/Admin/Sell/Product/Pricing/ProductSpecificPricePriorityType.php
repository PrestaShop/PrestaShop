<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Product\Pricing;

use PrestaShop\PrestaShop\Adapter\Product\SpecificPrice\Repository\SpecificPriceRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\ValueObject\PriorityList;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

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
