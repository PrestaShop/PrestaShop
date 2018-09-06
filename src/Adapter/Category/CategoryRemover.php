<?php
/**
 * 2007-2018 PrestaShop
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Category;

use Category;
use PrestaShop\PrestaShop\Core\Domain\Product\Category\ValueObject\CategoryDeletionMode;
use PrestaShop\PrestaShop\Core\Foundation\Database\DatabaseInterface;
use Product;
use Shop;
use Symfony\Component\Translation\TranslatorInterface;
use Validate;

/**
 * Class CategoryRemover is responsible for deleting legacy categories
 *
 * @internal
 */
class CategoryRemover
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var DatabaseInterface
     */
    private $database;

    /**
     * @param TranslatorInterface $translator
     * @param DatabaseInterface $database
     */
    public function __construct(
        TranslatorInterface $translator,
        DatabaseInterface $database
    ) {
        $this->translator = $translator;
        $this->database = $database;
    }

    /**
     * Delete single category
     *
     * @param int $categoryId
     * @param CategoryDeletionMode $mode
     *
     * @return string[] Errors if any
     */
    public function remove($categoryId, CategoryDeletionMode $mode)
    {
        $errors = [];

        $category = new Category($categoryId);

        if (!Validate::isLoadedObject($category)) {
            $errors[] = sprintf(
                '#%s. %s %s',
                $categoryId,
                $this->translator->trans(
                    'An error occurred while deleting the object.',
                    [],
                    'Admin.Notifications.Error'
                ),
                $this->translator->trans('(cannot load object)', [], 'Admin.Notifications.Error')
            );

            return $errors;
        }

        if ($category->isRootCategoryForAShop()) {
            $errors[] = sprintf(
                '#%s. %s',
                $categoryId,
                $this->translator->trans(
                    'You cannot remove this category because one of your shops uses it as a root category.',
                    [],
                    'Admin.Catalog.Notification'
                )
            );

            return $errors;
        }

        if (!$category->delete()) {
            $errors[] = $this->translator->trans(
                'Can\'t delete #%id%',
                ['%id%' => $categoryId],
                'Admin.Notifications.Error'
            );

            return $errors;
        }

        $this->handleProductsUpdate((int) $category->id_parent, $mode);

        return $errors;
    }

    /**
     * Delete multiple categories
     *
     * @param int[] $categoryIds
     * @param CategoryDeletionMode $mode
     *
     * @return string[] Errors if any
     */
    public function removeMultiple(array $categoryIds, CategoryDeletionMode $mode)
    {
        $errors = [];

        if (empty($categoryIds)) {
            $errors[] = $this->translator->trans(
                'You must select at least one element to delete.',
                [],
                'Admin.Notifications.Error'
            );

            return $errors;
        }

        foreach ($categoryIds as $categoryId) {
            $errors = array_merge(
                $errors,
                $this->remove($categoryId, $mode)
            );
        }

        return $errors;
    }

    /**
     * Handle products category after its deletion
     *
     * @param $parentCategoryId
     * @param CategoryDeletionMode $mode
     */
    private function handleProductsUpdate($parentCategoryId, CategoryDeletionMode $mode)
    {
        $productWithoutCategory = $this->database->select('
			SELECT p.`id_product`
			FROM `' . _DB_PREFIX_ . 'product` p
			' . Shop::addSqlAssociation('product', 'p') . '
			WHERE NOT EXISTS (
			    SELECT 1 FROM `' . _DB_PREFIX_ . 'category_product` cp WHERE cp.`id_product` = p.`id_product`
			)
		');

        foreach ($productWithoutCategory as $productWithoutCategory) {
            $product = new Product((int) $productWithoutCategory['id_product']);

            if (Validate::isLoadedObject($product)) {
                if (0 === $parentCategoryId || $mode->shouldRemoveProducts()) {
                    $product->delete();

                    continue;
                }

                if ($mode->shouldDisableProducts()) {
                    $product->active = 0;
                }

                $product->id_category_default = $parentCategoryId;
                $product->addToCategories($parentCategoryId);
                $product->save();
            }
        }
    }
}
