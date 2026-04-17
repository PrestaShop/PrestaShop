<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Presenter\Category;

use Category;
use Language;
use Link;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Presenter\AbstractLazyArray;
use PrestaShop\PrestaShop\Adapter\Presenter\LazyArrayAttribute;

class CategoryLazyArray extends AbstractLazyArray
{
    /**
     * @var ImageRetriever
     */
    private $imageRetriever;

    /**
     * @var Link
     */
    private $link;

    /**
     * @var array
     */
    protected $category;

    /**
     * @var Language
     */
    private $language;

    public function __construct(
        array $category,
        Language $language,
        ImageRetriever $imageRetriever,
        Link $link
    ) {
        $this->category = $category;
        $this->language = $language;
        $this->imageRetriever = $imageRetriever;
        $this->link = $link;

        parent::__construct();
        $this->appendArray($this->category);
    }

    /**
     * @return string
     */
    #[LazyArrayAttribute(arrayAccess: true)]
    public function getUrl()
    {
        return $this->link->getCategoryLink(
            $this->category['id'],
            $this->category['link_rewrite']
        );
    }

    /**
     * @return array|null
     */
    #[LazyArrayAttribute(arrayAccess: true)]
    public function getImage()
    {
        return $this->getCover();
    }

    /**
     * This returns category cover image (miniatures of CATEGORYID.jpg).
     * Used as a big image under category description.
     *
     * @return array|null
     */
    #[LazyArrayAttribute(arrayAccess: true)]
    public function getCover()
    {
        // Get image identifier for the thumbnail and check if it exists
        $imageIdentifier = $this->category['id'];
        if (!$this->doesCategoryImageExist($imageIdentifier)) {
            return null;
        }

        return $this->imageRetriever->getImage(
            new Category($this->category['id'], $this->language->getId()),
            $imageIdentifier
        );
    }

    /**
     * This returns category thumbnail image (miniatures of CATEGORYID_thumb.jpg).
     * Used for thumbnails in subcategories.
     *
     * @return array|null
     */
    #[LazyArrayAttribute(arrayAccess: true)]
    public function getThumbnail()
    {
        // Get image identifier for the thumbnail and check if it exists
        $imageIdentifier = $this->category['id'] . '_thumb';
        if (!$this->doesCategoryImageExist($imageIdentifier)) {
            return null;
        }

        return $this->imageRetriever->getImage(
            new Category($this->category['id'], $this->language->getId()),
            $imageIdentifier
        );
    }

    /**
     * Checks if given category image exists for our category.
     *
     * @return bool
     */
    private function doesCategoryImageExist($idImage)
    {
        return file_exists(_PS_CAT_IMG_DIR_ . $idImage . '.jpg');
    }
}
