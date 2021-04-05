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

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Defines thumbnail regeneration form.
 */
class ThumbnailRegenerationType extends TranslatorAwareType
{
    /**
     * @var FormChoiceProviderInterface
     */
    private $imageTypeCategoryChoiceProvider;

    /**
     * @var FormChoiceProviderInterface
     */
    private $imageTypeFormatsChoiceProvider;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param FormChoiceProviderInterface $imageTypeCategoryChoiceProvider
     * @param FormChoiceProviderInterface $imageTypeFormatsChoiceProvider
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        FormChoiceProviderInterface $imageTypeCategoryChoiceProvider,
        FormChoiceProviderInterface $imageTypeFormatsChoiceProvider
    ) {
        parent::__construct($translator, $locales);

        $this->imageTypeCategoryChoiceProvider = $imageTypeCategoryChoiceProvider;
        $this->imageTypeFormatsChoiceProvider = $imageTypeFormatsChoiceProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $categoryChoices = $this->imageTypeCategoryChoiceProvider->getChoices();
        $formatChoices = $this->imageTypeFormatsChoiceProvider->getChoices();

        $builder->add('image_category', ChoiceType::class, [
            'label' => $this->trans('Select an image', 'Admin.Design.Feature'),
            'choices' => $categoryChoices,
        ]);

        foreach ($categoryChoices as $choice) {
            if ($choice === 'all') {
                continue;
            }

            $choices = [];
            foreach ($formatChoices[$choice] as $fc) {
                $choices[$fc['name']] = $fc['id_image_type'];
            }

            $builder->add('format_' . $choice, ChoiceType::class, [
                'label' => $this->trans('Select a format', 'Admin.Design.Feature'),
                'choices' => $choices,
                'row_attr' => ['class' => 'd-none second-select format_' . $choice],
            ]);
        }

        $builder->add('erase_previous_images', SwitchType::class, [
            'label' => $this->trans('Erase previous images', 'Admin.Design.Feature'),
            'help' => $this->trans('Select "No" only if your server timed out and you need to resume the regeneration.', 'Admin.Design.Help'),
            'data' => true,
        ]);
    }
}
