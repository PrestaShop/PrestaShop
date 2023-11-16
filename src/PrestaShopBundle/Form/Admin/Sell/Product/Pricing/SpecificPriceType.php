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

use DateTime;
use PrestaShop\PrestaShop\Adapter\Attribute\Repository\AttributeRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DateRange;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\NoCombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Exception\SpecificPriceException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShop\PrestaShop\Core\Product\Combination\NameBuilder\CombinationNameBuilderInterface;
use PrestaShopBundle\Form\Admin\Sell\Customer\SearchedCustomerType;
use PrestaShopBundle\Form\Admin\Type\DateRangeType;
use PrestaShopBundle\Form\Admin\Type\EntitySearchInputType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Contracts\Translation\TranslatorInterface;

class SpecificPriceType extends TranslatorAwareType
{
    private const COMBINATION_RESULTS_LIMIT = 20;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var AttributeRepository
     */
    private $attributeRepository;

    /**
     * @var EventSubscriberInterface
     */
    private $specificPriceCombinationListener;

    /**
     * @var CombinationNameBuilderInterface
     */
    private $combinationNameBuilder;

    /**
     * @var int
     */
    private $languageId;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param UrlGeneratorInterface $urlGenerator
     * @param ProductRepository $productRepository
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        UrlGeneratorInterface $urlGenerator,
        ProductRepository $productRepository,
        AttributeRepository $attributeRepository,
        EventSubscriberInterface $specificPriceCombinationListener,
        CombinationNameBuilderInterface $combinationNameBuilder,
        int $contextLanguageId
    ) {
        parent::__construct($translator, $locales);
        $this->urlGenerator = $urlGenerator;
        $this->productRepository = $productRepository;
        $this->attributeRepository = $attributeRepository;
        $this->specificPriceCombinationListener = $specificPriceCombinationListener;
        $this->combinationNameBuilder = $combinationNameBuilder;
        $this->languageId = $contextLanguageId;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (!isset($builder->getData()['product_id'])) {
            // product_id is required for create action and to load combinations choices list, but it is not editable
            throw new SpecificPriceException('product_id is required to add/edit specific price.');
        }

        $builder
            ->add('product_id', HiddenType::class)
            ->add('groups', ApplicableGroupsType::class, [
                'label' => $this->trans('Apply to:', 'Admin.Global'),
                'required' => false,
            ])
            ->add('customer', EntitySearchInputType::class, [
                'label' => $this->trans('Apply to all customers', 'Admin.Global'),
                'layout' => EntitySearchInputType::LIST_LAYOUT,
                'entry_type' => SearchedCustomerType::class,
                'entry_options' => [
                    'block_prefix' => 'searched_customer',
                ],
                'allow_delete' => false,
                'limit' => 1,
                'disabling_switch' => true,
                'disabling_switch_event' => 'switchSpecificPriceCustomer',
                'switch_state_on_disable' => 'on',
                'disabled_value' => function ($data) {
                    return empty($data[0]['id_customer']);
                },
                'remote_url' => $this->urlGenerator->generate('admin_customers_search', ['customer_search' => '__QUERY__']),
                'placeholder' => $this->trans('Search customer', 'Admin.Actions'),
                'suggestion_field' => 'fullname_and_email',
                'required' => false,
            ])
        ;

        $productId = new ProductId((int) $builder->getData()['product_id']);
        $productType = $this->productRepository->getProductType($productId);
        if ($productType->getValue() === ProductType::TYPE_COMBINATIONS) {
            $builder->add('combination_id', ChoiceType::class, [
                'label' => $this->trans('Combination', 'Admin.Global'),
                'required' => false,
                'choices' => $this->getSelectedChoices($builder),
                'attr' => [
                    // select2 jQuery component is added in javascript manually for this ChoiceType
                    'data-minimum-results-for-search' => self::COMBINATION_RESULTS_LIMIT,
                    // we still need to pass all combinations choice to javascript
                    // to prepend it to the ajax-fetched list of combination choices
                    'data-all-combinations-label' => $this->getAllCombinationsChoiceLabel(),
                    'data-all-combinations-value' => NoCombinationId::NO_COMBINATION_ID,
                ],
            ]);
        }

        $builder
            ->add('from_quantity', NumberType::class, [
                'label' => $this->trans('Minimum number of units purchased', 'Admin.Catalog.Feature'),
                'scale' => 0,
                'constraints' => [
                    new GreaterThanOrEqual([
                        'value' => 0,
                        'message' => $this->trans(
                            '%s is invalid.',
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ])
            ->add('date_range', DateRangeType::class, [
                'label' => $this->trans('Duration', 'Admin.Catalog.Feature'),
                'label_tag_name' => 'h4',
                'required' => false,
                'has_unlimited_checkbox' => true,
                'date_format' => 'YYYY-MM-DD HH:mm:ss',
                'placeholder' => $this->trans('YYYY-MM-DD HH:mm:ss', 'Admin.Global'),
                'default_end_value' => (new DateTime())->modify('+1 month')->format('Y-m-d H:i:s'),
                'constraints' => [
                    new DateRange([
                        'message' => $this->trans(
                            'The selected date range is not valid.',
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
                'columns_number' => 2,
            ])
            ->add('impact', SpecificPriceImpactType::class)
        ;

        $builder->addEventSubscriber($this->specificPriceCombinationListener);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'label' => $this->trans('Conditions', 'Admin.Catalog.Feature'),
            'label_tag_name' => 'h4',
            'required' => false,
            'form_theme' => '@PrestaShop/Admin/Sell/Catalog/Product/SpecificPrice/FormTheme/specific_price.html.twig',
        ]);
    }

    /**
     * Provides choices list with a selected choice only (so it can be shown during page load).
     * All the other choices are retrieved through ajax in javascript side.
     *
     * @param FormBuilderInterface $builder
     *
     * @return array<string, int>
     */
    private function getSelectedChoices(FormBuilderInterface $builder): array
    {
        $combinationIdValue = $builder->getData()['combination_id'] ?? NoCombinationId::NO_COMBINATION_ID;

        return [
            $this->getCombinationName($combinationIdValue) => $combinationIdValue,
        ];
    }

    /**
     * @param int $combinationIdValue
     *
     * @return string
     */
    private function getCombinationName(int $combinationIdValue): string
    {
        if (NoCombinationId::NO_COMBINATION_ID === $combinationIdValue) {
            return $this->getAllCombinationsChoiceLabel();
        }

        $combinationId = new CombinationId($combinationIdValue);
        $attributesInformation = $this->attributeRepository->getAttributesInfoByCombinationIds(
            [$combinationId],
            new LanguageId($this->languageId)
        );

        return $this->combinationNameBuilder->buildName($attributesInformation[$combinationId->getValue()]);
    }

    private function getAllCombinationsChoiceLabel(): string
    {
        return $this->trans('All combinations', 'Admin.Global');
    }
}
