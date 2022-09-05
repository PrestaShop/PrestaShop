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
class QqUploadedFileFormCore
{
    /**
     * Save the file to the specified path.
     *
     * @return bool|array{"error": string} TRUE on success
     */
    public function save()
    {
        $product = new Product($_GET['id_product']);
        if (!Validate::isLoadedObject($product)) {
            return [
                'error' => Context::getContext()->getTranslator()->trans(
                    'Cannot add image because product creation failed.',
                    [],
                    'Admin.Catalog.Notification'
                ),
            ];
        } else {
            $image = new Image();
            $image->id_product = (int) $product->id;
            $image->position = Image::getHighestPosition($product->id) + 1;
            $legends = Tools::getValue('legend');
            if (is_array($legends)) {
                foreach ($legends as $key => $legend) {
                    if (Validate::isGenericName($legend)) {
                        $image->legend[(int) $key] = $legend;
                    } else {
                        return ['error' => Context::getContext()->getTranslator()->trans('Error on image caption: "%1s" is not a valid caption.', [Tools::safeOutput($legend)], 'Admin.Catalog.Notification')];
                    }
                }
            }
            $image->cover = !Image::getCover($image->id_product);

            if (($validate = $image->validateFieldsLang(false, true)) !== true) {
                return ['error' => $validate];
            }
            if (!$image->add()) {
                return ['error' => Context::getContext()->getTranslator()->trans('Error while creating additional image', [], 'Admin.Catalog.Notification')];
            } else {
                return $this->copyImage($product->id, $image->id);
            }
        }
    }

    public function copyImage($id_product, $id_image, $method = 'auto')
    {
        $image = new Image($id_image);
        if (!$new_path = $image->getPathForCreation()) {
            return ['error' => Context::getContext()->getTranslator()->trans('An error occurred while attempting to create a new folder.', [], 'Admin.Notifications.Error')];
        }
        if (!($tmpName = tempnam(_PS_TMP_IMG_DIR_, 'PS')) || !move_uploaded_file($_FILES['qqfile']['tmp_name'], $tmpName)) {
            return ['error' => Context::getContext()->getTranslator()->trans('An error occurred while uploading the image.', [], 'Admin.Notifications.Error')];
        } elseif (!ImageManager::resize($tmpName, $new_path . '.' . $image->image_format)) {
            return ['error' => Context::getContext()->getTranslator()->trans('An error occurred while copying the image.', [], 'Admin.Notifications.Error')];
        } elseif ($method == 'auto') {
            $imagesTypes = ImageType::getImagesTypes('products');
            foreach ($imagesTypes as $imageType) {
                if (!ImageManager::resize($tmpName, $new_path . '-' . stripslashes($imageType['name']) . '.' . $image->image_format, $imageType['width'], $imageType['height'], $image->image_format)) {
                    return ['error' => Context::getContext()->getTranslator()->trans('An error occurred while copying this image: %s', [stripslashes($imageType['name'])], 'Admin.Notifications.Error')];
                }
            }
        }
        unlink($tmpName);
        Hook::exec('actionWatermark', ['id_image' => $id_image, 'id_product' => $id_product]);

        if (!$image->update()) {
            return ['error' => Context::getContext()->getTranslator()->trans('Error while updating the status.', [], 'Admin.Notifications.Error')];
        }
        $img = ['id_image' => $image->id, 'position' => $image->position, 'cover' => $image->cover, 'name' => $this->getName(), 'legend' => $image->legend];

        return ['success' => $img];
    }

    public function getName()
    {
        return $_FILES['qqfile']['name'];
    }

    public function getSize()
    {
        return $_FILES['qqfile']['size'];
    }
}
