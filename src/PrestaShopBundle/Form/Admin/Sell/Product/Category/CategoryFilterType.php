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

namespace PrestaShopBundle\Form\Admin\Sell\Product\Category;

use PrestaShop\PrestaShop\Adapter\Category\CategoryDataProvider;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * This form type is used in the product list, it displays a tree to select one category used to filter the list.
 */
class CategoryFilterType extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var CategoryDataProvider
     */
    private $categoryProvider;

    /**
     * @var int
     */
    private $languageId;

    /**
     * @param TranslatorInterface $translator
     * @param CategoryDataProvider $categoryDataProvider
     * @param int $languageId
     */
    public function __construct(
        TranslatorInterface $translator,
        CategoryDataProvider $categoryDataProvider,
        int $languageId
    ) {
        $this->translator = $translator;
        $this->categoryProvider = $categoryDataProvider;
        $this->languageId = $languageId;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        $view->vars['nested_tree'] = $options['nested_tree'];
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $nestedTree = $this->categoryProvider->getNestedCategories(null, $this->languageId, false);
        $flattenedTree = $this->flattenTree($nestedTree);

        $resolver->setDefaults([
            'label' => $this->translator->trans('Categories', [], 'Admin.Catalog.Feature'),
            'choices' => $flattenedTree,
            'nested_tree' => $nestedTree,
            'multiple' => false,
            'expanded' => false,
            'required' => false,
            'choice_label' => 'name',
            'choice_value' => 'category_id',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return ChoiceType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'category_filter';
    }

    private function flattenTree(array $categories): array
    {
        $flattenedTree = [];
        foreach ($categories as $category) {
            $categoryId = (int) $category['id_category'];
            $flattenedTree[] = (object) [
                'category_id' => $categoryId,
                'name' => $category['name'],
            ];
            if (!empty($category['children'])) {
                $flattenedTree = array_merge(
                    $flattenedTree,
                    $this->flattenTree($category['children'])
                );
            }
        }

        return $flattenedTree;
    }
}
