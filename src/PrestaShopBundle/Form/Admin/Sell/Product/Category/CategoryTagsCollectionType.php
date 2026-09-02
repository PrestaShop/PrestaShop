<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Product\Category;

use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class CategoryTagsCollectionType extends CollectionType
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'label' => false,
            'required' => false,
            'block_prefix' => 'category_tags_collection',
            'entry_type' => ProductCategoryType::class,
            'entry_options' => [
                'block_prefix' => 'category_tags_collection_entry',
            ],
            'allow_add' => true,
            'allow_delete' => true,
            'prototype_name' => '__CATEGORY_INDEX__',
        ]);
    }

    /**
     * Get the translated chain from key.
     *
     * @param string $key the key to be translated
     * @param string $domain the domain to be selected
     * @param array $parameters Optional, pass parameters if needed (uncommon)
     *
     * @returns string
     */
    protected function trans($key, $domain, $parameters = [])
    {
        return $this->translator->trans($key, $parameters, $domain);
    }
}
