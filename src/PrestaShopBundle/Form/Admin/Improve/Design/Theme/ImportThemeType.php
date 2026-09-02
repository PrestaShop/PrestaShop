<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'post_max_size_message' => $this->trans(
                'The uploaded file is too large.',
                'Admin.Notifications.Error'
            ),
        ]);
    }
}
