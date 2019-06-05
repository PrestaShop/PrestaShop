<?php
/**
 * 2007-2019 PrestaShop and Contributors
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

namespace PrestaShop\PrestaShop\Adapter\Import;

use Image;
use ImageManager;
use ImageType;
use PrestaShop\PrestaShop\Adapter\Tools;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;

/**
 * Class ImageCopier copies images during import process.
 */
final class ImageCopier
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var Tools
     */
    private $tools;

    /**
     * @var int
     */
    private $contextShopId;

    /**
     * @var HookDispatcherInterface
     */
    private $hookDispatcher;

    /**
     * @param ConfigurationInterface $configuration
     * @param Tools $tools
     * @param int $contextShopId
     * @param HookDispatcherInterface $hookDispatcher
     */
    public function __construct(
        ConfigurationInterface $configuration,
        Tools $tools,
        $contextShopId,
        HookDispatcherInterface $hookDispatcher
    ) {
        $this->configuration = $configuration;
        $this->tools = $tools;
        $this->contextShopId = $contextShopId;
        $this->hookDispatcher = $hookDispatcher;
    }

    /**
     * Copy an image located in $url and save it in a path.
     *
     * @param int $entityId id of product or category (set in entity)
     * @param int $imageId id of the image if watermark enabled
     * @param string $url path or url to use
     * @param string $entity 'products' or 'categories'
     * @param bool $regenerate
     *
     * @return bool
     */
    public function copyImg($entityId, $imageId = null, $url = '', $entity = 'products', $regenerate = true)
    {
        $tmpDir = $this->configuration->get('_PS_TMP_IMG_DIR_');
        $tmpFile = tempnam($tmpDir, 'ps_import');
        $watermarkTypes = explode(',', $this->configuration->get('WATERMARK_TYPES'));

        switch ($entity) {
            default:
            case 'products':
                $image_obj = new Image($imageId);
                $path = $image_obj->getPathForCreation();
                break;
            case 'categories':
                $path = $this->configuration->get('_PS_CAT_IMG_DIR_') . (int) $entityId;
                break;
            case 'manufacturers':
                $path = $this->configuration->get('_PS_MANU_IMG_DIR_') . (int) $entityId;
                break;
            case 'suppliers':
                $path = $this->configuration->get('_PS_SUPP_IMG_DIR_') . (int) $entityId;
                break;
            case 'stores':
                $path = $this->configuration->get('_PS_STORE_IMG_DIR_') . (int) $entityId;
                break;
        }

        $url = urldecode(trim($url));
        $parsedUrl = parse_url($url);

        if (isset($parsedUrl['path'])) {
            $uri = ltrim($parsedUrl['path'], '/');
            $parts = explode('/', $uri);
            foreach ($parts as &$part) {
                $part = rawurlencode($part);
            }
            unset($part);
            $parsedUrl['path'] = '/' . implode('/', $parts);
        }

        if (isset($parsedUrl['query'])) {
            $query_parts = array();
            parse_str($parsedUrl['query'], $query_parts);
            $parsedUrl['query'] = http_build_query($query_parts);
        }

        if (!function_exists('http_build_url')) {
            require_once $this->configuration->get('_PS_TOOL_DIR_') . 'http_build_url/http_build_url.php';
        }

        $url = http_build_url('', $parsedUrl);

        $origTmpfile = $tmpFile;

        if ($this->tools->copy($url, $tmpFile)) {
            // Evaluate the memory required to resize the image: if it's too much, you can't resize it.
            if (!ImageManager::checkImageMemoryLimit($tmpFile)) {
                @unlink($tmpFile);

                return false;
            }

            $targetWidth = $targetHeight = 0;
            $sourceWidth = $sourceHeight = 0;
            $error = 0;
            ImageManager::resize(
                $tmpFile,
                $path . '.jpg',
                null,
                null,
                'jpg',
                false,
                $error,
                $targetWidth,
                $targetHeight,
                5,
                $sourceWidth,
                $sourceHeight
            );
            $imagesTypes = ImageType::getImagesTypes($entity, true);

            if ($regenerate) {
                $previous_path = null;
                $pathInfos = [];
                $pathInfos[] = [$targetWidth, $targetHeight, $path . '.jpg'];
                foreach ($imagesTypes as $imageType) {
                    $tmpFile = $this->getBestPath($imageType['width'], $imageType['height'], $pathInfos);

                    if (ImageManager::resize(
                        $tmpFile,
                        $path . '-' . stripslashes($imageType['name']) . '.jpg',
                        $imageType['width'],
                        $imageType['height'],
                        'jpg',
                        false,
                        $error,
                        $targetWidth,
                        $targetHeight,
                        5,
                        $sourceWidth,
                        $sourceHeight
                    )) {
                        // the last image should not be added in the candidate list if it's bigger than the original image
                        if ($targetWidth <= $sourceWidth && $targetHeight <= $sourceHeight) {
                            $pathInfos[] = array($targetWidth, $targetHeight, $path . '-' . stripslashes($imageType['name']) . '.jpg');
                        }
                        if ($entity == 'products') {
                            $file = $tmpDir . 'product_mini_' . (int) $entityId . '.jpg';
                            if (is_file($file)) {
                                unlink($file);
                            }

                            $file = $tmpDir . 'product_mini_' . (int) $entityId . '_' . (int) $this->contextShopId . '.jpg';
                            if (is_file($file)) {
                                unlink($file);
                            }
                        }
                    }
                    if (in_array($imageType['id_image_type'], $watermarkTypes)) {
                        $this->hookDispatcher->dispatchWithParameters(
                            'actionWatermark',
                            [
                                'id_image' => $imageId,
                                'id_product' => $entityId,
                            ]
                        );
                    }
                }
            }
        } else {
            @unlink($origTmpfile);

            return false;
        }
        unlink($origTmpfile);

        return true;
    }

    /**
     * Find the best path, compared to given dimensions.
     *
     * @param int $targetWidth
     * @param int $targetHeight
     * @param array $pathInfos
     *
     * @return string
     */
    private function getBestPath($targetWidth, $targetHeight, $pathInfos)
    {
        $pathInfos = array_reverse($pathInfos);
        $path = '';
        foreach ($pathInfos as $pathInfo) {
            list($width, $height, $path) = $pathInfo;
            if ($width >= $targetWidth && $height >= $targetHeight) {
                return $path;
            }
        }

        return $path;
    }
}
