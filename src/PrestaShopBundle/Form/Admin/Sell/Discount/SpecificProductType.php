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

namespace PrestaShopBundle\Form\Admin\Sell\Discount;

use PrestaShop\PrestaShop\Adapter\Attribute\Repository\AttributeRepository;
use PrestaShop\PrestaShop\Adapter\Product\Combination\Repository\CombinationRepository;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\NoCombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShop\PrestaShop\Core\Product\Combination\NameBuilder\CombinationNameBuilderInterface;
use PrestaShopBundle\Form\Admin\Type\ImagePreviewType;
use PrestaShopBundle\Form\Admin\Type\TextPreviewType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use PrestaShopBundle\Form\FormCloner;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Contracts\Translation\TranslatorInterface;

class SpecificProductType extends TranslatorAwareType
{
    private const COMBINATION_RESULTS_LIMIT = 20;

    public function __construct(
        private readonly CombinationNameBuilderInterface $combinationNameBuilder,
        private readonly AttributeRepository $attributeRepository,
        private readonly LanguageContext $languageContext,
        private readonly CombinationRepository $combinationRepository,
        private readonly FormCloner $formCloner,
        TranslatorInterface $translator,
        array $locales,
    ) {
        parent::__construct($translator, $locales);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'specific-product-id',
                ],
            ])
            ->add('product_type', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'specific-product-type',
                ],
            ])
            ->add('name', TextPreviewType::class, [
                'label' => false,
            ])
            ->add('image', ImagePreviewType::class, [
                'label' => false,
            ])
            ->add('combination_id', ChoiceType::class, [
                'label' => false,
                'required' => false,
                'placeholder' => null,
                'attr' => [
                    'class' => 'specific-combination-choice',
                    // select2 jQuery component is added in javascript manually for this ChoiceType
                    'data-minimum-results-for-search' => self::COMBINATION_RESULTS_LIMIT,
                    // we still need to pass all combinations choice to javascript
                    // to prepend it to the ajax-fetched list of combination choices
                    'data-all-combinations-label' => $this->getAllCombinationsChoiceLabel(),
                    'data-all-combinations-value' => NoCombinationId::NO_COMBINATION_ID,
                ],
                'choices' => [
                    $this->getAllCombinationsChoiceLabel() => NoCombinationId::NO_COMBINATION_ID,
                ],
                'row_attr' => [
                    'class' => 'specific-product-combination-row',
                ],
            ])
            ->add('quantity', IntegerType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'js-comma-transformer',
                ],
                'row_attr' => [
                    'class' => 'specific-product-quantity-row',
                ],
                'constraints' => [
                    new NotBlank([]),
                    new Type([
                        'type' => 'numeric',
                    ]),
                    new GreaterThanOrEqual([
                        'value' => 1,
                    ]),
                ],
            ])
        ;

        // Dynamically update combination choices to include the posted value
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $this->updateCombinationChoices($event);
        });
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $this->updateCombinationChoices($event);
        });
    }

    public function updateCombinationChoices(FormEvent $event): void
    {
        $form = $event->getForm();
        $data = $event->getData();

        // Prototype rendering
        if (empty($data)) {
            return;
        }

        // If product has no combinations we hide the combination selector
        if ($data['product_type'] !== ProductType::TYPE_COMBINATIONS) {
            $oldCombinationChoicesForm = $form->get('combination_id');
            $combinationOptions = $oldCombinationChoicesForm->getConfig()->getOptions();
            $combinationOptions['attr']['class'] = trim(($combinationOptions['attr']['class'] ?? '') . ' d-none');
            $newCombinationChoicesForm = $this->formCloner->cloneForm($oldCombinationChoicesForm, $combinationOptions);
        } else {
            // We need to update the choices list in the form, it must contain the default All combinations and the selected
            // combination if present, if the selected combination is not present in the choices an error is raised
            $combinationId = (int) $data['combination_id'];
            $choices = [
                $this->getAllCombinationsChoiceLabel() => NoCombinationId::NO_COMBINATION_ID,
            ];

            if ($combinationId !== NoCombinationId::NO_COMBINATION_ID) {
                $this->combinationRepository->assertCombinationExists(new CombinationId($combinationId));
                $choices[$this->getCombinationName($combinationId)] = $combinationId;
            }

            $newCombinationChoicesForm = $this->formCloner->cloneForm($form->get('combination_id'), [
                'choices' => $choices,
            ]);
        }

        $form->add($newCombinationChoicesForm);
    }

    /**
     * This block prefix is important it allows inheriting the templates from the default EntitySearchInputType
     *
     * @return string
     */
    public function getBlockPrefix(): string
    {
        return 'entity_item';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'form_theme' => '@PrestaShop/Admin/Sell/Catalog/Discount/FormTheme/specific_product.html.twig',
            'default_empty_data' => [
                // Force the default quantity to 1, and this allows using an IntegerType for quantity field as well
                'quantity' => 1,
            ],
        ]);
    }

    private function getCombinationName(int $combinationIdValue): string
    {
        if (NoCombinationId::NO_COMBINATION_ID === $combinationIdValue) {
            return $this->getAllCombinationsChoiceLabel();
        }

        $combinationId = new CombinationId($combinationIdValue);
        $attributesInformation = $this->attributeRepository->getAttributesInfoByCombinationIds(
            [$combinationId],
            new LanguageId($this->languageContext->getId())
        );

        return $this->combinationNameBuilder->buildName($attributesInformation[$combinationId->getValue()]);
    }

    private function getAllCombinationsChoiceLabel(): string
    {
        return $this->trans('All combinations', 'Admin.Global');
    }
}
