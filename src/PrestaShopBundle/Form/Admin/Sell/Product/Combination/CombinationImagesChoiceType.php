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

namespace PrestaShopBundle\Form\Admin\Sell\Product\Combination;

use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class CombinationImagesChoiceType extends TranslatorAwareType
{
    /**
     * @var ConfigurableFormChoiceProviderInterface
     */
    private $imagesChoiceProvider;

    /**
     * @param TranslatorInterface $translator
     * @param array<int, array<string, mixed>> $locales
     * @param ConfigurableFormChoiceProviderInterface $imagesChoiceProvider
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        ConfigurableFormChoiceProviderInterface $imagesChoiceProvider
    ) {
        parent::__construct($translator, $locales);
        $this->imagesChoiceProvider = $imagesChoiceProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver
            ->setRequired([
                'product_id',
            ])
            ->setAllowedTypes('product_id', 'int')
            ->setDefaults([
                'label' => $this->trans('Images', 'Admin.Global'),
                'label_subtitle' => $this->trans('You can specify which images should be displayed when customer selects this combination. If you don\'t select any image, all will be displayed. The default image of the combination will be the first one selected from the list.', 'Admin.Catalog.Feature'),
                'choice_attr' => function (string $choice, string $key): array {
                    return ['data-image-url' => $key];
                },
                'multiple' => true,
                'expanded' => true,
            ]
        );

        $choiceProvider = $this->imagesChoiceProvider;
        $resolver->setNormalizer('choices', function (OptionsResolver $resolver) use ($choiceProvider) {
            $productId = $resolver->offsetGet('product_id');

            return $choiceProvider->getChoices(['product_id' => $productId]);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
