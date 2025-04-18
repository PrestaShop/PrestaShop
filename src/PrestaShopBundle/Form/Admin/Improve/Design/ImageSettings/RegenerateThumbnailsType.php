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

namespace PrestaShopBundle\Form\Admin\Improve\Design\ImageSettings;

use PrestaShop\PrestaShop\Core\Form\ChoiceProvider\ImageTypeChoiceProvider;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegenerateThumbnailsType extends TranslatorAwareType
{
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        private readonly ImageTypeChoiceProvider $imageTypeChoiceProvider
    ) {
        parent::__construct($translator, $locales);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('image', ChoiceType::class, [
                'label' => $this->trans('Select an image', 'Admin.Design.Feature'),
                'attr' => [
                    'data-formats' => json_encode($this->imageTypeChoiceProvider->buildChoicesByTypes()),
                ],
                'constraints' => [
                    new NotBlank(),
                ],
                'choices' => [
                    $this->trans('All', 'Admin.Global') => 'all',
                    $this->trans('Categories', 'Admin.Global') => 'categories',
                    $this->trans('Brands', 'Admin.Global') => 'manufacturers',
                    $this->trans('Suppliers', 'Admin.Global') => 'suppliers',
                    $this->trans('Products', 'Admin.Global') => 'products',
                    $this->trans('Stores', 'Admin.Global') => 'stores',
                ],
            ])
            ->add('image-type', ChoiceType::class, [
                'label' => $this->trans('Select a format', 'Admin.Design.Feature'),
                'constraints' => [
                    new NotBlank(),
                ],
                'choices' => [
                    $this->trans('All', 'Admin.Global') => 0,
                    ...$this->imageTypeChoiceProvider->getChoices(),
                ],
            ])
            ->add('erase-previous-images', SwitchType::class, [
                'label' => $this->trans('Erase previous images', 'Admin.Design.Feature'),
                'help' => $this->trans('Select "No" only if your server timed out and you need to resume the regeneration.', 'Admin.Design.Help'),
                'required' => false,
                'data' => false,
            ])
        ;
    }
}
