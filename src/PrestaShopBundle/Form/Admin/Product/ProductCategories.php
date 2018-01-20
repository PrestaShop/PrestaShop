<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShopBundle\Form\Admin\Product;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use PrestaShopBundle\Form\Admin\Type\ChoiceCategoriesTreeType;
use PrestaShop\PrestaShop\Adapter\Category\CategoryDataProvider;

/**
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
    private $languageId;

    /**
     * @param TranslatorInterface $translator
     * @param CategoryDataProvider $categoryDataProvider
     * @param int $languageId
     */
    public function __construct(
        TranslatorInterface $translator,
        CategoryDataProvider $categoryDataProvider,
        $languageId
    )
    {
        $this->categoryProvider = $categoryDataProvider;
        parent::__construct($translator);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('categories', ChoiceCategoriesTreeType::class, array(
            'label' => $this->trans('Categories', 'Admin.Catalog.Feature'),
            'list' => $this->categoryProvider->getNestedCategories(null, $this->languageId, false),
            'valid_list' => [],
            'multiple' => false,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'product_categories';
    }
}
