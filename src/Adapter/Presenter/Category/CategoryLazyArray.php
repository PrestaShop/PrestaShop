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
