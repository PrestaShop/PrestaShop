<?php
/**
 * 2007-2016 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */


/**
 * Handle file uploads via XMLHttpRequest
 */
class QqUploadedFileXhrCore
{
    /**
     * Save the file to the specified path
     *
     * @param string $path
     *
     * @return bool `true` on success
     */
    public function upload($path)
    {
        $input = fopen('php://input', 'r');
        $target = fopen($path, 'w');

        $realSize = stream_copy_to_stream($input, $target);
        if ($realSize != $this->getSize()) {
            return false;
        }

        fclose($input);
        fclose($target);

        return true;
    }

    /**
     * @return array
     */
    public function save()
    {
        $product = new Product($_GET['id_product']);
        if (!Validate::isLoadedObject($product)) {
            return array('error' => Tools::displayError('Cannot add image because product creation failed.'));
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
                        return array('error' => sprintf(Tools::displayError('Error on image caption: "%1s" is not a valid caption.'), Tools::safeOutput($legend)));
                    }
                }
            }
            if (!Image::getCover($image->id_product)) {
                $image->cover = 1;
            } else {
                $image->cover = 0;
            }

            if (($validate = $image->validateFieldsLang(false, true)) !== true) {
                return array('error' => Tools::displayError($validate));
            }
            if (!$image->add()) {
                return array('error' => Tools::displayError('Error while creating additional image'));
            } else {
                return $this->copyImage($product->id, $image->id);
            }
        }
    }

    /**
     * @param  int   $idProduct Product ID
     * @param  int   $idImage   Image ID
     * @param string $method
     *
     * @return array
     */
    public function copyImage($idProduct, $idImage, $method = 'auto')
    {
        $image = new Image($idImage);
        if (!$newPath = $image->getPathForCreation()) {
            return array('error' => Tools::displayError('An error occurred during new folder creation'));
        }
        if (!($tmpName = tempnam(_PS_TMP_IMG_DIR_, 'PS')) || !$this->upload($tmpName)) {
            return array('error' => Tools::displayError('An error occurred during the image upload'));
        } elseif (!ImageManager::resize($tmpName, $newPath.'.'.$image->image_format)) {
            return array('error' => Tools::displayError('An error occurred while copying image.'));
        } elseif ($method == 'auto') {
            $imagesTypes = ImageType::getImagesTypes('products');
            foreach ($imagesTypes as $imageType) {
                if (!ImageManager::resize($tmpName, $newPath.'-'.stripslashes($imageType['name']).'.'.$image->image_format, $imageType['width'], $imageType['height'], $image->image_format)) {
                    return array('error' => Tools::displayError('An error occurred while copying image:').' '.stripslashes($imageType['name']));
                }
            }
        }
        unlink($tmpName);
        Hook::exec('actionWatermark', array('id_image' => $idImage, 'id_product' => $idProduct));

        if (!$image->update()) {
            return array('error' => Tools::displayError('Error while updating status'));
        }
        $img = array('id_image' => $image->id, 'position' => $image->position, 'cover' => $image->cover, 'name' => $this->getName(), 'legend' => $image->legend);

        return array('success' => $img);
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $_GET['qqfile'];
    }

    /**
     * @return bool|int
     */
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
