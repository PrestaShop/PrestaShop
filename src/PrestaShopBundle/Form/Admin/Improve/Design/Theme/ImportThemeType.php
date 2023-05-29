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

namespace PrestaShopBundle\Form\Admin\Improve\Design\Theme;

use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ImportThemeType
 */
class ImportThemeType extends TranslatorAwareType
{
    /**
     * @var array
     */
    private $themeZipsChoices;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param array $themeZipsChoices
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        array $themeZipsChoices
    ) {
        parent::__construct($translator, $locales);
        $this->themeZipsChoices = $themeZipsChoices;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('import_from_computer', FileType::class, [
                'label' => $this->trans('Zip file', 'Admin.Design.Feature'),
                'help' => $this->trans(
                    'Browse your computer files and select the Zip file for your new theme.',
                    'Admin.Design.Help'
                ),
                'required' => false,
                'constraints' => new File([
                    'mimeTypes' => 'application/zip',
                    'mimeTypesMessage' => $this->trans('Invalid file format.', 'Admin.Design.Notification'),
                ]),
            ])
            ->add('import_from_web', UrlType::class, [
                'label' => $this->trans('Archive URL', 'Admin.Design.Feature'),
                'help' => $this->trans(
                    'Indicate the complete URL to an online Zip file that contains your new theme. For instance, "http://example.com/files/theme.zip".',
                    'Admin.Design.Help'
                ),
                'required' => false,
            ])
            ->add('import_from_ftp', ChoiceType::class, [
                'label' => $this->trans('Select the archive', 'Admin.Design.Feature'),
                'help' => $this->trans(
                    'This selector lists the Zip files that you uploaded in the \'/themes\' folder.',
                    'Admin.Design.Help'
                ),
                'required' => false,
                'placeholder' => '-',
                'choices' => $this->themeZipsChoices,
                'translation_domain' => false,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'post_max_size_message' => $this->trans(
                'The uploaded file is too large.',
                'Admin.Notifications.Error'
            ),
        ]);
    }
}
