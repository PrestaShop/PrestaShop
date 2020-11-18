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

/**
 * Handle file uploads via XMLHttpRequest.
 */
class QqUploadedFileXhrCore
{
    /**
     * Save the file to the specified path.
     *
     * @return bool TRUE on success
     */
    public function upload($path)
    {
        $input = fopen('php://input', 'rb');
        $target = fopen($path, 'wb');

        $realSize = stream_copy_to_stream($input, $target);
        if ($realSize != $this->getSize()) {
            return false;
        }

        fclose($input);
        fclose($target);

        return true;
    }

    public function save()
    {
        $product = new Product($_GET['id_product']);
        if (!Validate::isLoadedObject($product)) {
            return array('error' => Context::getContext()->getTranslator()->trans('Cannot add image because product creation failed.', array(), 'Admin.Catalog.Notification'));
        } else {
            $image = new Image();
            $image->id_product = (int) ($product->id);
            $image->position = Image::getHighestPosition($product->id) + 1;
            $legends = Tools::getValue('legend');
            if (is_array($legends)) {
                foreach ($legends as $key => $legend) {
                    if (Validate::isGenericName($legend)) {
                        $image->legend[(int) $key] = $legend;
                    } else {
                        return array('error' => Context::getContext()->getTranslator()->trans('Error on image caption: "%1s" is not a valid caption.', array(Tools::safeOutput($legend)), 'Admin.Notifications.Error'));
                    }
                }
            }
            if (!Image::getCover($image->id_product)) {
                $image->cover = 1;
            } else {
                $image->cover = 0;
            }

            if (($validate = $image->validateFieldsLang(false, true)) !== true) {
                return array('error' => $validate);
            }
            if (!$image->add()) {
                return array('error' => Context::getContext()->getTranslator()->trans('Error while creating additional image', array(), 'Admin.Catalog.Notification'));
            } else {
                return $this->copyImage($product->id, $image->id);
            }
        }
    }

    public function copyImage($id_product, $id_image, $method = 'auto')
    {
        $image = new Image($id_image);
        if (!$new_path = $image->getPathForCreation()) {
            return array('error' => Context::getContext()->getTranslator()->trans('An error occurred while attempting to create a new folder.', array(), 'Admin.Notifications.Error'));
        }
        if (!($tmpName = tempnam(_PS_TMP_IMG_DIR_, 'PS')) || !$this->upload($tmpName)) {
            return array('error' => Context::getContext()->getTranslator()->trans('An error occurred while uploading the image.', array(), 'Admin.Notifications.Error'));
        } elseif (!ImageManager::resize($tmpName, $new_path . '.' . $image->image_format)) {
            return array('error' => Context::getContext()->getTranslator()->trans('An error occurred while uploading the image.', array(), 'Admin.Notifications.Error'));
        } elseif ($method == 'auto') {
            $imagesTypes = ImageType::getImagesTypes('products');
            foreach ($imagesTypes as $imageType) {
                if (!ImageManager::resize($tmpName, $new_path . '-' . stripslashes($imageType['name']) . '.' . $image->image_format, $imageType['width'], $imageType['height'], $image->image_format)) {
                    return array('error' => Context::getContext()->getTranslator()->trans('An error occurred while copying this image: %s', array(stripslashes($imageType['name'])), 'Admin.Notifications.Error'));
                }
            }
        }
        unlink($tmpName);
        Hook::exec('actionWatermark', array('id_image' => $id_image, 'id_product' => $id_product));

        if (!$image->update()) {
            return array('error' => Context::getContext()->getTranslator()->trans('Error while updating the status.', array(), 'Admin.Notifications.Error'));
        }
        $img = array('id_image' => $image->id, 'position' => $image->position, 'cover' => $image->cover, 'name' => $this->getName(), 'legend' => $image->legend);

        return array('success' => $img);
    }

    public function getName()
    {
        return $_GET['qqfile'];
    }

    public function getSize()
    {
        if (isset($_SERVER['CONTENT_LENGTH']) || isset($_SERVER['HTTP_CONTENT_LENGTH'])) {
            if (isset($_SERVER['HTTP_CONTENT_LENGTH'])) {
                return (int) $_SERVER['HTTP_CONTENT_LENGTH'];
            } else {
                return (int) $_SERVER['CONTENT_LENGTH'];
            }
        }

        return false;
    }
}
