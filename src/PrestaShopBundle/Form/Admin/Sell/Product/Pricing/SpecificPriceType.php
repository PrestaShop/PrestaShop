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

use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DateRange;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Exception\SpecificPriceException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Sell\Customer\SearchedCustomerType;
use PrestaShopBundle\Form\Admin\Type\DateRangeType;
use PrestaShopBundle\Form\Admin\Type\EntitySearchInputType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;

class SpecificPriceType extends TranslatorAwareType
{
    /**
     * @var ConfigurableFormChoiceProviderInterface
     */
    private $combinationIdChoiceProvider;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param ConfigurableFormChoiceProviderInterface $combinationIdChoiceProvider
     * @param UrlGeneratorInterface $urlGenerator
     * @param ProductRepository $productRepository
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        ConfigurableFormChoiceProviderInterface $combinationIdChoiceProvider,
        UrlGeneratorInterface $urlGenerator,
        ProductRepository $productRepository
    ) {
        parent::__construct($translator, $locales);
        $this->combinationIdChoiceProvider = $combinationIdChoiceProvider;
        $this->urlGenerator = $urlGenerator;
        $this->productRepository = $productRepository;
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
                'placeholder' => $this->trans('All combinations', 'Admin.Global'),
                'choices' => $this->combinationIdChoiceProvider->getChoices(['product_id' => $builder->getData()['product_id']]),
                'required' => false,
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
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'label' => $this->trans('Conditions', 'Admin.Catalog.Feature'),
            'label_tag_name' => 'h4',
            'required' => false,
        ]);
    }
}
