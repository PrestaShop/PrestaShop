<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Presenter\Category;

use Category;
use Hook;
use Language;
use Link;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;

class CategoryPresenter
{
    /**
     * @var ImageRetriever
     */
    protected $imageRetriever;

    /**
     * @var Link
     */
    protected $link;

    public function __construct(Link $link)
    {
        $this->link = $link;
        $this->imageRetriever = new ImageRetriever($link);
    }

    /**
     * @param array|Category $category Category object or an array
     * @param Language $language
     *
     * @return CategoryLazyArray
     */
    public function present(array|Category $category, Language $language): CategoryLazyArray
    {
        // Convert to array if a Category object was passed
        if (is_object($category)) {
            $category = (array) $category;
        }

        // Normalize IDs
        if (empty($category['id_category'])) {
            $category['id_category'] = $category['id'];
        }
        if (empty($category['id'])) {
            $category['id'] = $category['id_category'];
        }

        $categoryLazyArray = new CategoryLazyArray(
            $category,
            $language,
            $this->imageRetriever,
            $this->link
        );

        Hook::exec('actionPresentCategory',
            ['presentedCategory' => &$categoryLazyArray]
        );

        return $categoryLazyArray;
    }
}
