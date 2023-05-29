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

namespace PrestaShopBundle\Form\Admin\Product;

use PrestaShop\PrestaShop\Adapter\Category\CategoryDataProvider;
use PrestaShopBundle\Form\Admin\Type\ChoiceCategoriesTreeType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @deprecated since 8.1 and will be removed in next major.
 *
 * This class render Product Categories Form in Product List Page.
 */
class ProductCategories extends TranslatorAwareType
{
    /**
     * @var CategoryDataProvider
     */
    private $categoryProvider;

    /**
     * @var int
     */
    private $contextLangId;

    /**
     * @param TranslatorInterface $translator
     * @param CategoryDataProvider $categoryDataProvider
     * @param array $locales
     * @param int $contextLangId
     */
    public function __construct(
        TranslatorInterface $translator,
        CategoryDataProvider $categoryDataProvider,
        array $locales,
        int $contextLangId
    ) {
        $this->categoryProvider = $categoryDataProvider;
        $this->contextLangId = $contextLangId;
        parent::__construct($translator, $locales);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('categories', ChoiceCategoriesTreeType::class, [
            'label' => $this->trans('Categories', 'Admin.Catalog.Feature'),
            'list' => $this->categoryProvider->getNestedCategories(null, $this->contextLangId, false),
            'valid_list' => [],
            'multiple' => false,
            'expanded' => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'product_categories';
    }
}
