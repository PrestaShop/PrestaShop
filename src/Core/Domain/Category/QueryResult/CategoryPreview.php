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

namespace PrestaShop\PrestaShop\Core\Domain\Category\QueryResult;

/**
 * Minimum data to display a preview of a category
 */
class CategoryPreview
{
    /**
     * @var int
     */
    private $categoryId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $breadcrumb;

    /**
     * @var string
     */
    private $image;

    /**
     * @param int $categoryId
     * @param string $name
     * @param string $breadcrumb
     * @param string $imageUrl
     */
    public function __construct(
        int $categoryId,
        string $name,
        string $breadcrumb,
        string $imageUrl
    ) {
        $this->categoryId = $categoryId;
        $this->name = $name;
        $this->breadcrumb = $breadcrumb;
        $this->image = $imageUrl;
    }

    /**
     * @return int
     */
    public function getCategoryId(): int
    {
        return $this->categoryId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getBreadcrumb(): string
    {
        return $this->breadcrumb;
    }

    /**
     * @return string
     */
    public function getImage(): string
    {
        return $this->image;
    }
}
