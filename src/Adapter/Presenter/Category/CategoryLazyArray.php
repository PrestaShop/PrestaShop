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
     * @arrayAccess
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->link->getCategoryLink(
            $this->category['id'],
            $this->category['link_rewrite']
        );
    }

    /**
     * @arrayAccess
     *
     * This method returns standardized category image array created from CATEGORYID.jpg, with one exception.
     * One thumbnail size - CATEGORYID-small_default.jpg is generated from CATEGORYID_thumb.jpg instead.
     * This must be resolved in the future.
     *
     * @return array|null
     */
    public function getImage()
    {
        /*
         * Category is a bit different that other objects. It's image ID is defined by a special id_image property.
         * When constructing Category object model, this value is a numeric ID if image exists or false if it doesn't.
         * In case of getSubCategories method, this value is a numeric ID if image exists OR a default "not found" image if it doesn't.
         */
        if (empty($this->category['id_image'])) {
            return null;
        }

        return $this->imageRetriever->getImage(
            new Category($this->category['id'], $this->language->getId()),
            $this->category['id_image']
        );
    }

    /**
     * @arrayAccess
     *
     * @return array|null
     */
    public function getCover()
    {
        return $this->getImage();
    }

    /**
     * @todo This should return category thumbnail image (miniatures of CATEGORYID_thumb.jpg) instead,
     * after support in ImageRetriever is implemented.
     *
     * @arrayAccess
     *
     * @return array|null
     */
    public function getThumbnail()
    {
        return $this->getImage();
    }
}
