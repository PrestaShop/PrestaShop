<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\OrderState;

use HelperList;
use ImageManager;
use PrestaShop\PrestaShop\Core\Image\ImageProviderInterface;
use PrestaShop\PrestaShop\Core\Image\Parser\ImageTagSourceParserInterface;

/**
 * Class OrderStateIconProvider provides path to order_state's icon.
 */
final class OrderStateIconProvider implements ImageProviderInterface
{
    /**
     * @var ImageTagSourceParserInterface
     */
    private $imageTagSourceParser;

    /**
     * @var int
     */
    private $contextShopId;

    /**
     * @param ImageTagSourceParserInterface $imageTagSourceParser
     * @param $contextShopId
     */
    public function __construct(
        ImageTagSourceParserInterface $imageTagSourceParser,
        $contextShopId
    ) {
        $this->imageTagSourceParser = $imageTagSourceParser;
        $this->contextShopId = $contextShopId;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath($orderStatusId)
    {
        $pathToImage = rtrim(_PS_ORDER_STATE_IMG_DIR_, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $orderStatusId . '.gif';

        $imageTag = ImageManager::thumbnail(
            $pathToImage,
            $orderStatusId . '.gif',
            HelperList::LIST_THUMBNAIL_SIZE,
            'gif'
        );

        return $this->imageTagSourceParser->parse($imageTag);
    }
}
