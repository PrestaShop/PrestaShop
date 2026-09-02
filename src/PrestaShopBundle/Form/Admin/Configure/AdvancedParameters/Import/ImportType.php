<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Import;

use PrestaShop\PrestaShop\Core\Import\Entity;
use PrestaShopBundle\Form\Admin\Type\LocaleChoiceType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This form class generates the "Import" form in Import page.
 */
class ImportType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('csv', HiddenType::class)
            ->add('entity', ChoiceType::class, [
                'required' => true,
                'label' => $this->trans('What do you want to import?', 'Admin.Advparameters.Feature'),
                'row_attr' => [
                    'class' => 'js-entity-select',
                ],
                'choices' => [
                    $this->trans('Categories', 'Admin.Global') => Entity::TYPE_CATEGORIES,
                    $this->trans('Products', 'Admin.Global') => Entity::TYPE_PRODUCTS,
                    $this->trans('Combinations', 'Admin.Global') => Entity::TYPE_COMBINATIONS,
                    $this->trans('Customers', 'Admin.Global') => Entity::TYPE_CUSTOMERS,
                    $this->trans('Addresses', 'Admin.Global') => Entity::TYPE_ADDRESSES,
                    $this->trans('Brands', 'Admin.Global') => Entity::TYPE_MANUFACTURERS,
                    $this->trans('Suppliers', 'Admin.Global') => Entity::TYPE_SUPPLIERS,
                    $this->trans('Alias', 'Admin.Shopparameters.Feature') => Entity::TYPE_ALIAS,
                    $this->trans('Store contacts', 'Admin.Advparameters.Feature') => Entity::TYPE_STORE_CONTACTS,
                ],
            ])
            ->add('file', FileType::class, [
                'required' => false,
                'label' => $this->trans('Select a file to import', 'Admin.Advparameters.Feature'),
            ])
            ->add('iso_lang', LocaleChoiceType::class, [
                'required' => true,
                'placeholder' => null,
                'label' => $this->trans('Language of the file', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('The locale must be installed', 'Admin.Advparameters.Notification'),
            ])
            ->add('separator', TextType::class, [
                'label' => $this->trans('Field separator', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('e.g. 1; Blouse; 129.90; 5', 'Admin.Advparameters.Help'),
            ])
            ->add('multiple_value_separator', TextType::class, [
                'label' => $this->trans('Multiple value separator', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('e.g. Blouse; red.jpg, blue.jpg, green.jpg; 129.90 ', 'Admin.Advparameters.Help'),
            ])
            ->add('truncate', SwitchType::class, [
                'row_attr' => [
                    'class' => 'js-truncate-form-group',
                ],
                'label' => $this->trans(
                    'Delete all [1]categories[/1] before import',
                    'Admin.Advparameters.Feature',
                    [
                        '[1]' => '<span class="js-entity-name">',
                        '[/1]' => '</span>',
                    ]
                ),
            ])
            ->add('match_ref', SwitchType::class, [
                'row_attr' => [
                    'class' => 'js-match-ref-form-group',
                ],
                'label' => $this->trans('Use product reference as key', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('If enabled, the product\'s reference number MUST be unique!', 'Admin.Advparameters.Help'),
            ])
            ->add('regenerate', SwitchType::class, [
                'row_attr' => [
                    'class' => 'js-regenerate-form-group',
                ],
                'label' => $this->trans('Skip thumbnails regeneration', 'Admin.Advparameters.Feature'),
            ])
            ->add('forceIDs', SwitchType::class, [
                'row_attr' => [
                    'class' => 'js-force-ids-form-group',
                ],
                'label' => $this->trans('Force all ID numbers', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Enable this option to keep your imported items’ ID number as is. Otherwise, PrestaShop will ignore them and create auto-incremented ID numbers.', 'Admin.Advparameters.Help'),
            ])
            ->add('sendemail', SwitchType::class, [
                'label' => $this->trans('Send notification email', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Receive an email when the import is complete. It can be useful when handling large files, as the import may take some time.', 'Admin.Advparameters.Help'),
            ])
            ->add('submitImportFile', SubmitType::class, [
                'label' => $this->trans('Next step', 'Admin.Advparameters.Feature'),
            ]);

        $builder->get('entity')
            ->addModelTransformer(new CallbackTransformer(
                function ($entity) {
                    if (null === $entity) {
                        return $entity;
                    }

                    return is_numeric($entity) ? $entity : Entity::getFromName($entity);
                },
                function ($entity) {
                    return $entity;
                }
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'Admin.Shopparameters.Feature',
        ]);
    }
}
