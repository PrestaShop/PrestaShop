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

namespace PrestaShop\PrestaShop\Adapter\Shop\Url;

use Link;
use PrestaShop\PrestaShop\Core\Shop\Url\UrlProviderInterface;

@trigger_error(
    sprintf(
        '%s is deprecated since version 1.7.9.0 and will be removed in the next major version. Use %s instead.',
        ProductImageFolderProvider::class,
        ImageFolderProvider::class
    ),
    E_USER_DEPRECATED
);

/**
 * @deprecated Since 1.7.9.0 and will be removed in the next major version. Use ImageFolderProvider instead.
 */
class ProductImageFolderProvider implements UrlProviderInterface
{
    /**
     * @var Link
     */
    private $link;

    /**
     * @var string
     */
    private $imagesRelativeFolder;

    /**
     * @param Link $link
     * @param string $imagesRelativeFolder
     */
    public function __construct(
        Link $link,
        string $imagesRelativeFolder
    ) {
        $this->link = $link;
        $this->imagesRelativeFolder = $imagesRelativeFolder;
    }

    /**
     * Create a link to product images base folder.
     *
     * @return string
     */
    public function getUrl(): string
    {
        return rtrim($this->link->getBaseLink(), '/') . '/' . rtrim($this->imagesRelativeFolder, '/');
    }
}
