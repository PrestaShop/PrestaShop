<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\Search;

use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PositiveOrZero;

class SearchOptionsType extends TranslatorAwareType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('search_within_word', SwitchType::class, [
                'label' => $this->trans('Search within word', 'Admin.Shopparameters.Feature'),
                'help' => $this->trans(
                    'By default, to search for "blouse", you have to enter "blous", "blo", etc (beginning of the word) – but not "lous" (within the word).',
                    'Admin.Shopparameters.Help'
                ) . '<br/>' . $this->trans(
                    'With this option enabled, it also gives the good result if you search for "lous", "ouse", or anything contained in the word.',
                    'Admin.Shopparameters.Help'
                ),
                'required' => false,
            ])
            ->add('search_exact_end_match', SwitchType::class, [
                'label' => $this->trans('Search exact end match', 'Admin.Shopparameters.Feature'),
                'help' => $this->trans(
                    'By default, if you search "book", you will have "book", "bookcase" and "bookend".',
                    'Admin.Shopparameters.Help'
                ) . '<br/>' . $this->trans(
                    'With this option enabled, it only gives one result "book", as exact end of the indexed word is matching.',
                    'Admin.Shopparameters.Help'
                ),
                'required' => false,
            ])
            ->add('fuzzy_search', SwitchType::class, [
                'label' => $this->trans('Fuzzy search', 'Admin.Shopparameters.Feature'),
                'help' => $this->trans(
                    'By default, the fuzzy search is enabled. It means spelling errors are allowed, e.g. you can search for "bird" with words like "burd", "bard" or "beerd".',
                    'Admin.Shopparameters.Help'
                ) . '<br/>' . $this->trans(
                    'Disabling this option will require exact spelling for the search to match results.',
                    'Admin.Shopparameters.Help'
                ),
                'required' => false,
            ])
            ->add('fuzzy_max_words', IntegerType::class, [
                'label' => $this->trans('Maximum approximate words allowed by fuzzy search', 'Admin.Shopparameters.Feature'),
                'help' => $this->trans(
                    'Note that this option is resource-consuming: the more you search, the longer it takes.',
                    'Admin.Shopparameters.Help'
                ),
                'required' => false,
                'constraints' => [new PositiveOrZero()],
            ])
            ->add('fuzzy_max_difference', IntegerType::class, [
                'label' => $this->trans('Maximum acceptable word difference', 'Admin.Shopparameters.Feature'),
                'help' => $this->trans(
                    'This option defines how much different can the alternative words found by fuzzy search be. Or, how many characters can be different/missing/added. The default value is 5.',
                    'Admin.Shopparameters.Help'
                ),
                'required' => false,
                'constraints' => [new PositiveOrZero()],
            ])
            ->add('max_word_length', IntegerType::class, [
                'label' => $this->trans('Maximum word length (in characters)', 'Admin.Shopparameters.Feature'),
                'help' => $this->trans(
                    'Only words fewer or equal to this maximum length will be searched.',
                    'Admin.Shopparameters.Help'
                ),
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    new PositiveOrZero(),
                ],
            ])
            ->add('min_word_length', IntegerType::class, [
                'label' => $this->trans('Minimum word length (in characters)', 'Admin.Shopparameters.Feature'),
                'help' => $this->trans(
                    'Only words this size or larger will be indexed.',
                    'Admin.Shopparameters.Help'
                ),
                'required' => false,
                'constraints' => [new PositiveOrZero()],
            ])
            ->add('blacklisted_words', TranslatableType::class, [
                'label' => $this->trans('Blacklisted words', 'Admin.Shopparameters.Feature'),
                'help' => $this->trans(
                    'Please enter the index words separated by a "|".',
                    'Admin.Shopparameters.Help'
                ),
                'type' => TextareaType::class,
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_domain' => 'Admin.Shopparameters.Feature',
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'search_options_block';
    }
}
