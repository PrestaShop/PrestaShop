<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Improve\International\Translations;

use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Counterpart of ExportCataloguesType: takes back a language pack the shop exported, or one
 * downloaded from the translation project.
 */
class ImportLanguagePackType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('pack', FileType::class, [
            'label' => $this->trans('Language pack', 'Admin.International.Feature'),
            'help' => $this->trans(
                'A zip archive holding one folder named after the locale, for instance "fr-FR", with the XLF catalogues inside it. The language must already be installed.',
                'Admin.International.Help'
            ),
            'required' => true,
            'constraints' => [
                new NotBlank([
                    'message' => $this->trans('Select a language pack to import.', 'Admin.International.Notification'),
                ]),
                // The mime type only filters the obvious mistakes; what the archive really holds is
                // settled by TranslationPackValidator before anything is extracted.
                new File([
                    'mimeTypes' => ['application/zip', 'application/x-zip-compressed', 'application/octet-stream'],
                    'mimeTypesMessage' => $this->trans('Invalid file format.', 'Admin.International.Notification'),
                ]),
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'post_max_size_message' => $this->trans('The uploaded file is too large.', 'Admin.Notifications.Error'),
        ]);
    }
}
