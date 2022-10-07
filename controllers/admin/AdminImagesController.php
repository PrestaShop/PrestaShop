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

/**
 * @property ImageType $object
 */
class AdminImagesControllerCore extends AdminController
{
    protected $start_time = 0;
    protected $max_execution_time = 7200;
    protected $display_move;

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'image_type';
        $this->className = 'ImageType';
        $this->lang = false;

        $this->addRowAction('edit');
        $this->addRowAction('delete');

        parent::__construct();

        $this->bulk_actions = [
            'delete' => [
                'text' => $this->trans('Delete selected', [], 'Admin.Actions'),
                'confirm' => $this->trans('Delete selected items?', [], 'Admin.Notifications.Warning'),
                'icon' => 'icon-trash',
            ],
        ];

        $this->fields_list = [
            'id_image_type' => ['title' => $this->trans('ID', [], 'Admin.Global'), 'align' => 'center', 'class' => 'fixed-width-xs'],
            'name' => ['title' => $this->trans('Name', [], 'Admin.Global')],
            'width' => ['title' => $this->trans('Width', [], 'Admin.Global'),  'suffix' => ' px'],
            'height' => ['title' => $this->trans('Height', [], 'Admin.Global'),  'suffix' => ' px'],
            'products' => ['title' => $this->trans('Products', [], 'Admin.Global'), 'align' => 'center', 'type' => 'bool', 'callback' => 'printEntityActiveIcon', 'orderby' => false],
            'categories' => ['title' => $this->trans('Categories', [], 'Admin.Global'), 'align' => 'center', 'type' => 'bool', 'callback' => 'printEntityActiveIcon', 'orderby' => false],
            'manufacturers' => ['title' => $this->trans('Brands', [], 'Admin.Global'), 'align' => 'center', 'type' => 'bool', 'callback' => 'printEntityActiveIcon', 'orderby' => false],
            'suppliers' => ['title' => $this->trans('Suppliers', [], 'Admin.Global'), 'align' => 'center', 'type' => 'bool', 'callback' => 'printEntityActiveIcon', 'orderby' => false],
            'stores' => ['title' => $this->trans('Stores', [], 'Admin.Global'), 'align' => 'center', 'type' => 'bool', 'callback' => 'printEntityActiveIcon', 'orderby' => false],
        ];

        // No need to display the old image system migration tool except if product images are in _PS_PRODUCT_IMG_DIR_
        $this->display_move = false;
        $dir = _PS_PRODUCT_IMG_DIR_;
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false && $this->display_move == false) {
                    if (!is_dir($dir . DIRECTORY_SEPARATOR . $file) && $file[0] != '.' && is_numeric($file[0])) {
                        $this->display_move = true;
                    }
                }
                closedir($dh);
            }
        }

        $this->fields_options = [
            'images' => [
                'title' => $this->trans('Images generation options', [], 'Admin.Design.Feature'),
                'icon' => 'icon-picture',
                'top' => '',
                'bottom' => '',
                'description' => $this->trans('JPEG images have a small file size and standard quality. PNG images have a larger file size, a higher quality and support transparency. Note that in all cases the image files will have the .jpg extension.', [], 'Admin.Design.Help') . '
					<br /><br />' . $this->trans('WARNING: This feature may not be compatible with your theme, or with some of your modules. In particular, PNG mode is not compatible with the Watermark module. If you encounter any issues, turn it off by selecting "Use JPEG".', [], 'Admin.Design.Help'),
                'fields' => [
                    'PS_IMAGE_QUALITY' => [
                        'title' => $this->trans('Image format', [], 'Admin.Design.Feature'),
                        'show' => true,
                        'required' => true,
                        'type' => 'radio',
                        'choices' => [
                            'jpg' => $this->trans('Use JPEG.', [], 'Admin.Design.Feature'),
                            'png' => $this->trans('Use PNG only if the base image is in PNG format.', [], 'Admin.Design.Feature'),
                            'png_all' => $this->trans('Use PNG for all images.', [], 'Admin.Design.Feature'),
                            'webp' => $this->trans('Use WebP only if the base image is in WebP format.', [], 'Admin.Design.Feature'),
                            'webp_all' => $this->trans('Use WebP for all images.', [], 'Admin.Design.Feature'),
                        ],
                    ],
                    'PS_JPEG_QUALITY' => [
                        'title' => $this->trans('JPEG compression', [], 'Admin.Design.Feature'),
                        'hint' => $this->trans('Ranges from 0 (worst quality, smallest file) to 100 (best quality, biggest file).', [], 'Admin.Design.Help') . ' ' . $this->trans('Recommended: 90.', [], 'Admin.Design.Help'),
                        'validation' => 'isUnsignedId',
                        'required' => true,
                        'cast' => 'intval',
                        'type' => 'text',
                    ],
                    'PS_PNG_QUALITY' => [
                        'title' => $this->trans('PNG compression', [], 'Admin.Design.Feature'),
                        'hint' => $this->trans('PNG compression is lossless: unlike JPG, you do not lose image quality with a high compression ratio. However, photographs will compress very badly.', [], 'Admin.Design.Help') . ' ' . $this->trans('Ranges from 0 (biggest file) to 9 (smallest file, slowest decompression).', [], 'Admin.Design.Help') . ' ' . $this->trans('Recommended: 7.', [], 'Admin.Design.Help'),
                        'validation' => 'isUnsignedId',
                        'required' => true,
                        'cast' => 'intval',
                        'type' => 'text',
                    ],
                    'PS_WEBP_QUALITY' => [
                        'title' => $this->trans('WebP compression', [], 'Admin.Design.Feature'),
                        'hint' => $this->trans(
                                'Ranges from 0 (worst quality, smallest file) to 100 (best quality, biggest file).',
                                [],
                                'Admin.Design.Help'
                            ) .
                            ' ' .
                            $this->trans('Recommended: %d.', [80], 'Admin.Design.Help'),
                        'validation' => 'isUnsignedId',
                        'required' => true,
                        'cast' => 'intval',
                        'type' => 'text',
                    ],
                    'PS_IMAGE_GENERATION_METHOD' => [
                        'title' => $this->trans('Generate images based on one side of the source image', [], 'Admin.Design.Feature'),
                        'validation' => 'isUnsignedId',
                        'required' => false,
                        'cast' => 'intval',
                        'type' => 'select',
                        'list' => [
                            [
                                'id' => '0',
                                'name' => $this->trans('Automatic (longest side)', [], 'Admin.Design.Feature'),
                            ],
                            [
                                'id' => '1',
                                'name' => $this->trans('Width', [], 'Admin.Global'),
                            ],
                            [
                                'id' => '2',
                                'name' => $this->trans('Height', [], 'Admin.Global'),
                            ],
                        ],
                        'identifier' => 'id',
                        'visibility' => Shop::CONTEXT_ALL,
                    ],
                    'PS_PRODUCT_PICTURE_MAX_SIZE' => [
                        'title' => $this->trans('Maximum file size of product customization pictures', [], 'Admin.Design.Feature'),
                        'hint' => $this->trans('The maximum file size of pictures that customers can upload to customize a product (in bytes).', [], 'Admin.Design.Help'),
                        'validation' => 'isUnsignedInt',
                        'required' => true,
                        'cast' => 'intval',
                        'type' => 'text',
                        'suffix' => $this->trans('bytes', [], 'Admin.Design.Feature'),
                        'visibility' => Shop::CONTEXT_ALL,
                    ],
                    'PS_PRODUCT_PICTURE_WIDTH' => [
                        'title' => $this->trans('Product picture width', [], 'Admin.Design.Feature'),
                        'hint' => $this->trans('Width of product customization pictures that customers can upload (in pixels).', [], 'Admin.Design.Help'),
                        'validation' => 'isUnsignedInt',
                        'required' => true,
                        'cast' => 'intval',
                        'type' => 'text',
                        'width' => 'px',
                        'suffix' => $this->trans('pixels', [], 'Admin.Design.Feature'),
                        'visibility' => Shop::CONTEXT_ALL,
                    ],
                    'PS_PRODUCT_PICTURE_HEIGHT' => [
                        'title' => $this->trans('Product picture height', [], 'Admin.Design.Feature'),
                        'hint' => $this->trans('Height of product customization pictures that customers can upload (in pixels).', [], 'Admin.Design.Help'),
                        'validation' => 'isUnsignedInt',
                        'required' => true,
                        'cast' => 'intval',
                        'type' => 'text',
                        'height' => 'px',
                        'suffix' => $this->trans('pixels', [], 'Admin.Design.Feature'),
                        'visibility' => Shop::CONTEXT_ALL,
                    ],
                    'PS_HIGHT_DPI' => [
                        'type' => 'bool',
                        'title' => $this->trans('Generate high resolution images', [], 'Admin.Design.Feature'),
                        'required' => false,
                        'is_bool' => true,
                        'hint' => $this->trans('This will generate an additional file for each image (thus doubling your total amount of images). Resolution of these images will be twice higher.', [], 'Admin.Design.Help'),
                        'desc' => $this->trans('Enable to optimize the display of your images on high pixel density screens.', [], 'Admin.Design.Help'),
                        'visibility' => Shop::CONTEXT_ALL,
                    ],
                ],
                'submit' => ['title' => $this->trans('Save', [], 'Admin.Actions')],
            ],
        ];

        if ($this->display_move) {
            $this->fields_options['product_images']['fields']['PS_LEGACY_IMAGES'] = [
                'title' => $this->trans('Use the legacy image filesystem', [], 'Admin.Design.Feature'),
                'hint' => $this->trans('This should be set to yes unless you successfully moved images in "Images" page under the "Preferences" menu.', [], 'Admin.Design.Help'),
                'validation' => 'isBool',
                'cast' => 'intval',
                'required' => false,
                'type' => 'bool',
                'visibility' => Shop::CONTEXT_ALL,
            ];
        }

        $this->fields_form = [
            'legend' => [
                'title' => $this->trans('Image type', [], 'Admin.Design.Feature'),
                'icon' => 'icon-picture',
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->trans('Name for the image type', [], 'Admin.Design.Feature'),
                    'name' => 'name',
                    'required' => true,
                    'hint' => $this->trans('Letters, underscores and hyphens only (e.g. "small_custom", "cart_medium", "large", "thickbox_extra-large").', [], 'Admin.Design.Help'),
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Width', [], 'Admin.Global'),
                    'name' => 'width',
                    'required' => true,
                    'maxlength' => 5,
                    'suffix' => $this->trans('pixels', [], 'Admin.Design.Feature'),
                    'hint' => $this->trans('Maximum image width in pixels.', [], 'Admin.Design.Help'),
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Height', [], 'Admin.Global'),
                    'name' => 'height',
                    'required' => true,
                    'maxlength' => 5,
                    'suffix' => $this->trans('pixels', [], 'Admin.Design.Feature'),
                    'hint' => $this->trans('Maximum image height in pixels.', [], 'Admin.Design.Help'),
                ],
                [
                    'type' => 'switch',
                    'label' => $this->trans('Products', [], 'Admin.Global'),
                    'name' => 'products',
                    'required' => false,
                    'is_bool' => true,
                    'hint' => $this->trans('This type will be used for Product images.', [], 'Admin.Design.Help'),
                    'values' => [
                        [
                            'id' => 'products_on',
                            'value' => 1,
                            'label' => $this->trans('Yes', [], 'Admin.Global'),
                        ],
                        [
                            'id' => 'products_off',
                            'value' => 0,
                            'label' => $this->trans('No', [], 'Admin.Global'),
                        ],
                    ],
                ],
                [
                    'type' => 'switch',
                    'label' => $this->trans('Categories', [], 'Admin.Global'),
                    'name' => 'categories',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'hint' => $this->trans('This type will be used for Category images.', [], 'Admin.Design.Help'),
                    'values' => [
                        [
                            'id' => 'categories_on',
                            'value' => 1,
                            'label' => $this->trans('Yes', [], 'Admin.Global'),
                        ],
                        [
                            'id' => 'categories_off',
                            'value' => 0,
                            'label' => $this->trans('No', [], 'Admin.Global'),
                        ],
                    ],
                ],
                [
                    'type' => 'switch',
                    'label' => $this->trans('Brands', [], 'Admin.Global'),
                    'name' => 'manufacturers',
                    'required' => false,
                    'is_bool' => true,
                    'hint' => $this->trans('This type will be used for Brand images.', [], 'Admin.Design.Help'),
                    'values' => [
                        [
                            'id' => 'manufacturers_on',
                            'value' => 1,
                            'label' => $this->trans('Yes', [], 'Admin.Global'),
                        ],
                        [
                            'id' => 'manufacturers_off',
                            'value' => 0,
                            'label' => $this->trans('No', [], 'Admin.Global'),
                        ],
                    ],
                ],
                [
                    'type' => 'switch',
                    'label' => $this->trans('Suppliers', [], 'Admin.Global'),
                    'name' => 'suppliers',
                    'required' => false,
                    'is_bool' => true,
                    'hint' => $this->trans('This type will be used for Supplier images.', [], 'Admin.Design.Help'),
                    'values' => [
                        [
                            'id' => 'suppliers_on',
                            'value' => 1,
                            'label' => $this->trans('Yes', [], 'Admin.Global'),
                        ],
                        [
                            'id' => 'suppliers_off',
                            'value' => 0,
                            'label' => $this->trans('No', [], 'Admin.Global'),
                        ],
                    ],
                ],
                [
                    'type' => 'switch',
                    'label' => $this->trans('Stores', [], 'Admin.Global'),
                    'name' => 'stores',
                    'required' => false,
                    'is_bool' => true,
                    'hint' => $this->trans('This type will be used for Store images.', [], 'Admin.Design.Help'),
                    'values' => [
                        [
                            'id' => 'stores_on',
                            'value' => 1,
                            'label' => $this->trans('Yes', [], 'Admin.Global'),
                        ],
                        [
                            'id' => 'stores_off',
                            'value' => 0,
                            'label' => $this->trans('No', [], 'Admin.Global'),
                        ],
                    ],
                ],
            ],
            'submit' => [
                'title' => $this->trans('Save', [], 'Admin.Actions'),
            ],
        ];
    }

    public function postProcess()
    {
        // When moving images, if duplicate images were found they are moved to a folder named duplicates/
        if (file_exists(_PS_PRODUCT_IMG_DIR_ . 'duplicates/')) {
            $this->warnings[] = $this->trans('Duplicate images were found when moving the product images. This is likely caused by unused demonstration images. Please make sure that the folder %folder% only contains demonstration images, and then delete it.', ['%folder%' => _PS_PRODUCT_IMG_DIR_ . 'duplicates/'], 'Admin.Design.Notification');
        }

        if (Tools::isSubmit('submitRegenerate' . $this->table)) {
            if ($this->access('edit')) {
                if ($this->_regenerateThumbnails(Tools::getValue('type'), Tools::getValue('erase'))) {
                    Tools::redirectAdmin(self::$currentIndex . '&conf=9' . '&token=' . $this->token);
                }
            } else {
                $this->errors[] = $this->trans('You do not have permission to edit this.', [], 'Admin.Notifications.Error');
            }
        } elseif (Tools::isSubmit('submitMoveImages' . $this->table)) {
            if ($this->access('edit')) {
                if ($this->_moveImagesToNewFileSystem()) {
                    Tools::redirectAdmin(self::$currentIndex . '&conf=25' . '&token=' . $this->token);
                }
            } else {
                $this->errors[] = $this->trans('You do not have permission to edit this.', [], 'Admin.Notifications.Error');
            }
        } elseif (Tools::isSubmit('submitOptions' . $this->table)) {
            if ($this->access('edit')) {
                if ((int) Tools::getValue('PS_JPEG_QUALITY') < 0
                    || (int) Tools::getValue('PS_JPEG_QUALITY') > 100) {
                    $this->errors[] = $this->trans('Incorrect value for the selected JPEG image compression.', [], 'Admin.Design.Notification');
                } elseif ((int) Tools::getValue('PS_PNG_QUALITY') < 0
                    || (int) Tools::getValue('PS_PNG_QUALITY') > 9) {
                    $this->errors[] = $this->trans('Incorrect value for the selected PNG image compression.', [], 'Admin.Design.Notification');
                } elseif ((int) Tools::getValue('PS_WEBP_QUALITY') < 0
                    || (int) Tools::getValue('PS_WEBP_QUALITY') > 100) {
                    $this->errors[] = $this->trans('Incorrect value for the selected WebP image compression.', [], 'Admin.Design.Notification');
                } elseif (!Configuration::updateValue('PS_IMAGE_QUALITY', Tools::getValue('PS_IMAGE_QUALITY'))
                    || !Configuration::updateValue('PS_JPEG_QUALITY', Tools::getValue('PS_JPEG_QUALITY'))
                    || !Configuration::updateValue('PS_PNG_QUALITY', Tools::getValue('PS_PNG_QUALITY'))
                    || !Configuration::updateValue('PS_WEBP_QUALITY', Tools::getValue('PS_WEBP_QUALITY'))) {
                    $this->errors[] = $this->trans('Unknown error.', [], 'Admin.Notifications.Error');
                } else {
                    $this->confirmations[] = $this->_conf[6];
                }

                return parent::postProcess();
            } else {
                $this->errors[] = $this->trans('You do not have permission to edit this.', [], 'Admin.Notifications.Error');
            }
        } else {
            return parent::postProcess();
        }
    }

    public static function printEntityActiveIcon($value, $object)
    {
        return $value ? '<span class="list-action-enable action-enabled"><i class="icon-check"></i></span>' : '<span class="list-action-enable action-disabled"><i class="icon-remove"></i></span>';
    }

    protected function _childValidation()
    {
        if (!Tools::getValue('id_image_type') && Validate::isImageTypeName($typeName = Tools::getValue('name')) && ImageType::typeAlreadyExists($typeName)) {
            $this->errors[] = $this->trans('This name already exists.', [], 'Admin.Design.Notification');
        }
    }

    /**
     * Init display for the thumbnails regeneration block.
     */
    public function initRegenerate()
    {
        $types = [
            'categories' => $this->trans('Categories', [], 'Admin.Global'),
            'manufacturers' => $this->trans('Brands', [], 'Admin.Global'),
            'suppliers' => $this->trans('Suppliers', [], 'Admin.Global'),
            'products' => $this->trans('Products', [], 'Admin.Global'),
            'stores' => $this->trans('Stores', [], 'Admin.Global'),
        ];

        $formats = [];
        foreach ($types as $i => $type) {
            $formats[$i] = ImageType::getImagesTypes($i);
        }

        $this->context->smarty->assign([
            'types' => $types,
            'formats' => $formats,
        ]);
    }

    /**
     * Delete resized image then regenerate new one with updated settings.
     *
     * @param string $dir
     * @param array $type
     * @param bool $product
     *
     * @return bool
     */
    protected function _deleteOldImages($dir, $type, $product = false)
    {
        if (!is_dir($dir)) {
            return false;
        }
        $toDel = scandir($dir, SCANDIR_SORT_NONE);

        foreach ($toDel as $d) {
            foreach ($type as $imageType) {
                if (preg_match('/^[0-9]+\-' . ($product ? '[0-9]+\-' : '') . $imageType['name'] . '(|2x)\.jpg$/', $d)
                    || (count($type) > 1 && preg_match('/^[0-9]+\-[_a-zA-Z0-9-]*\.jpg$/', $d))
                    || preg_match('/^([[:lower:]]{2})\-default\-' . $imageType['name'] . '(|2x)\.jpg$/', $d)) {
                    if (file_exists($dir . $d)) {
                        unlink($dir . $d);
                    }
                }
            }
        }

        // delete product images using new filesystem.
        if ($product) {
            $productsImages = Image::getAllImages();
            foreach ($productsImages as $image) {
                $imageObj = new Image($image['id_image']);
                $imageObj->id_product = $image['id_product'];
                if (file_exists($dir . $imageObj->getImgFolder())) {
                    $toDel = scandir($dir . $imageObj->getImgFolder(), SCANDIR_SORT_NONE);
                    foreach ($toDel as $d) {
                        foreach ($type as $imageType) {
                            if (preg_match('/^[0-9]+\-' . $imageType['name'] . '(|2x)\.jpg$/', $d) || (count($type) > 1 && preg_match('/^[0-9]+\-[_a-zA-Z0-9-]*\.jpg$/', $d))) {
                                if (file_exists($dir . $imageObj->getImgFolder() . $d)) {
                                    unlink($dir . $imageObj->getImgFolder() . $d);
                                }
                            }
                        }
                    }
                }
            }
        }

        return true;
    }

    /**
     * Regenerate images.
     *
     * @param string $dir
     * @param array $type
     * @param bool $productsImages
     *
     * @return bool|string
     */
    protected function _regenerateNewImages($dir, $type, $productsImages = false)
    {
        if (!is_dir($dir)) {
            return false;
        }

        $generate_hight_dpi_images = (bool) Configuration::get('PS_HIGHT_DPI');

        if (!$productsImages) {
            $formated_medium = ImageType::getFormattedName('medium');
            foreach (scandir($dir, SCANDIR_SORT_NONE) as $image) {
                if (preg_match('/^[0-9]*\.jpg$/', $image)) {
                    foreach ($type as $k => $imageType) {
                        // Customizable writing dir
                        $newDir = $dir;
                        if (!file_exists($newDir)) {
                            continue;
                        }

                        if (($dir == _PS_CAT_IMG_DIR_) && ($imageType['name'] == $formated_medium) && is_file(_PS_CAT_IMG_DIR_ . str_replace('.', '_thumb.', $image))) {
                            $image = str_replace('.', '_thumb.', $image);
                        }

                        if (!file_exists($newDir . substr($image, 0, -4) . '-' . stripslashes($imageType['name']) . '.jpg')) {
                            if (!file_exists($dir . $image) || !filesize($dir . $image)) {
                                $this->errors[] = $this->trans('Source file does not exist or is empty (%filepath%)', ['%filepath%' => $dir . $image], 'Admin.Design.Notification');
                            } elseif (!ImageManager::resize($dir . $image, $newDir . substr(str_replace('_thumb.', '.', $image), 0, -4) . '-' . stripslashes($imageType['name']) . '.jpg', (int) $imageType['width'], (int) $imageType['height'])) {
                                $this->errors[] = $this->trans('Failed to resize image file (%filepath%)', ['%filepath%' => $dir . $image], 'Admin.Design.Notification');
                            }

                            if ($generate_hight_dpi_images) {
                                if (!ImageManager::resize($dir . $image, $newDir . substr($image, 0, -4) . '-' . stripslashes($imageType['name']) . '2x.jpg', (int) $imageType['width'] * 2, (int) $imageType['height'] * 2)) {
                                    $this->errors[] = $this->trans('Failed to resize image file to high resolution (%filepath%)', ['%filepath%' => $dir . $image], 'Admin.Design.Notification');
                                }
                            }
                        }
                        // stop 4 seconds before the timeout, just enough time to process the end of the page on a slow server
                        if (time() - $this->start_time > $this->max_execution_time - 4) {
                            return 'timeout';
                        }
                    }
                }
            }
        } else {
            foreach (Image::getAllImages() as $image) {
                $imageObj = new Image($image['id_image']);
                $existing_img = $dir . $imageObj->getExistingImgPath() . '.jpg';
                if (file_exists($existing_img) && filesize($existing_img)) {
                    foreach ($type as $imageType) {
                        if (!file_exists($dir . $imageObj->getExistingImgPath() . '-' . stripslashes($imageType['name']) . '.jpg')) {
                            if (!ImageManager::resize($existing_img, $dir . $imageObj->getExistingImgPath() . '-' . stripslashes($imageType['name']) . '.jpg', (int) $imageType['width'], (int) $imageType['height'])) {
                                $this->errors[] = $this->trans(
                                    'Original image is corrupt (%filename%) for product ID %id% or bad permission on folder.',
                                    [
                                        '%filename%' => $existing_img,
                                        '%id%' => (int) $imageObj->id_product,
                                    ],
                                    'Admin.Design.Notification'
                                );
                            }
                        }
                        if ($generate_hight_dpi_images) {
                            if (!file_exists($dir . $imageObj->getExistingImgPath() . '-' . stripslashes($imageType['name']) . '2x.jpg')) {
                                if (!ImageManager::resize($existing_img, $dir . $imageObj->getExistingImgPath() . '-' . stripslashes($imageType['name']) . '2x.jpg', (int) $imageType['width'] * 2, (int) $imageType['height'] * 2)) {
                                    $this->errors[] = $this->trans(
                                        'Original image is corrupt (%filename%) for product ID %id% or bad permission on folder.',
                                        [
                                            '%filename%' => $existing_img,
                                            '%id%' => (int) $imageObj->id_product,
                                        ],
                                        'Admin.Design.Notification'
                                    );
                                }
                            }
                        }
                    }
                } else {
                    $this->errors[] = $this->trans(
                        'Original image is missing or empty (%filename%) for product ID %id%',
                        [
                            '%filename%' => $existing_img,
                            '%id%' => (int) $imageObj->id_product,
                        ],
                        'Admin.Design.Notification'
                    );
                }
                if (time() - $this->start_time > $this->max_execution_time - 4) { // stop 4 seconds before the tiemout, just enough time to process the end of the page on a slow server
                    return 'timeout';
                }
            }
        }

        return (bool) count($this->errors);
    }

    /**
     * Regenerate no-pictures images.
     *
     * @param string $dir
     * @param array $type
     * @param array $languages
     *
     * @return bool
     */
    protected function _regenerateNoPictureImages($dir, $type, $languages)
    {
        $errors = false;
        $generate_hight_dpi_images = (bool) Configuration::get('PS_HIGHT_DPI');

        foreach ($type as $image_type) {
            foreach ($languages as $language) {
                $file = $dir . $language['iso_code'] . '.jpg';
                if (!file_exists($file)) {
                    $file = _PS_PRODUCT_IMG_DIR_ . Language::getIsoById((int) Configuration::get('PS_LANG_DEFAULT')) . '.jpg';
                }
                if (!file_exists($dir . $language['iso_code'] . '-default-' . stripslashes($image_type['name']) . '.jpg')) {
                    if (!ImageManager::resize($file, $dir . $language['iso_code'] . '-default-' . stripslashes($image_type['name']) . '.jpg', (int) $image_type['width'], (int) $image_type['height'])) {
                        $errors = true;
                    }

                    if ($generate_hight_dpi_images) {
                        if (!ImageManager::resize($file, $dir . $language['iso_code'] . '-default-' . stripslashes($image_type['name']) . '2x.jpg', (int) $image_type['width'] * 2, (int) $image_type['height'] * 2)) {
                            $errors = true;
                        }
                    }
                }
            }
        }

        return $errors;
    }

    /* Hook watermark optimization */
    protected function _regenerateWatermark($dir, $type = null)
    {
        $result = Db::getInstance()->executeS('
		SELECT m.`name` FROM `' . _DB_PREFIX_ . 'module` m
		LEFT JOIN `' . _DB_PREFIX_ . 'hook_module` hm ON hm.`id_module` = m.`id_module`
		LEFT JOIN `' . _DB_PREFIX_ . 'hook` h ON hm.`id_hook` = h.`id_hook`
		WHERE h.`name` = \'actionWatermark\' AND m.`active` = 1');

        if ($result && count($result)) {
            $productsImages = Image::getAllImages();
            foreach ($productsImages as $image) {
                $imageObj = new Image($image['id_image']);
                if (file_exists($dir . $imageObj->getExistingImgPath() . '.jpg')) {
                    foreach ($result as $module) {
                        $moduleInstance = Module::getInstanceByName($module['name']);
                        if ($moduleInstance && is_callable([$moduleInstance, 'hookActionWatermark'])) {
                            call_user_func([$moduleInstance, 'hookActionWatermark'], ['id_image' => $imageObj->id, 'id_product' => $imageObj->id_product, 'image_type' => $type]);
                        }

                        if (time() - $this->start_time > $this->max_execution_time - 4) { // stop 4 seconds before the tiemout, just enough time to process the end of the page on a slow server
                            return 'timeout';
                        }
                    }
                }
            }
        }
    }

    protected function _regenerateThumbnails($type = 'all', $deleteOldImages = false)
    {
        $this->start_time = time();
        ini_set('max_execution_time', $this->max_execution_time); // ini_set may be disabled, we need the real value
        $this->max_execution_time = (int) ini_get('max_execution_time');
        $languages = Language::getLanguages(false);

        $process = [
            ['type' => 'categories', 'dir' => _PS_CAT_IMG_DIR_],
            ['type' => 'manufacturers', 'dir' => _PS_MANU_IMG_DIR_],
            ['type' => 'suppliers', 'dir' => _PS_SUPP_IMG_DIR_],
            ['type' => 'products', 'dir' => _PS_PRODUCT_IMG_DIR_],
            ['type' => 'stores', 'dir' => _PS_STORE_IMG_DIR_],
        ];

        // Launching generation process
        foreach ($process as $proc) {
            if ($type != 'all' && $type != $proc['type']) {
                continue;
            }

            // Getting format generation
            $formats = ImageType::getImagesTypes($proc['type']);
            if ($type != 'all') {
                $format = (string) (Tools::getValue('format_' . $type));
                if ($format != 'all') {
                    foreach ($formats as $k => $form) {
                        if ($form['id_image_type'] != $format) {
                            unset($formats[$k]);
                        }
                    }
                }
            }

            if ($deleteOldImages) {
                $this->_deleteOldImages($proc['dir'], $formats, ($proc['type'] == 'products' ? true : false));
            }
            if (($return = $this->_regenerateNewImages($proc['dir'], $formats, ($proc['type'] == 'products' ? true : false))) === true) {
                if (!count($this->errors)) {
                    $this->errors[] = $this->trans('Cannot write images for this type: %1$s. Please check the %2$s folder\'s writing permissions.', [$proc['type'], $proc['dir']], 'Admin.Design.Notification');
                }
            } elseif ($return == 'timeout') {
                $this->errors[] = $this->trans('Only part of the images have been regenerated. The server timed out before finishing.', [], 'Admin.Design.Notification');
            } else {
                if ($proc['type'] == 'products') {
                    if ($this->_regenerateWatermark($proc['dir'], $formats) == 'timeout') {
                        $this->errors[] = $this->trans('Server timed out. The watermark may not have been applied to all images.', [], 'Admin.Design.Notification');
                    }
                }
                if (!count($this->errors)) {
                    if ($this->_regenerateNoPictureImages($proc['dir'], $formats, $languages)) {
                        $this->errors[] = $this->trans('Cannot write "No picture" image to %s images folder. Please check the folder\'s writing permissions.', [$proc['type']], 'Admin.Design.Notification');
                    }
                }
            }
        }

        return count($this->errors) > 0 ? false : true;
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_image_type'] = [
                'href' => self::$currentIndex . '&addimage_type&token=' . $this->token,
                'desc' => $this->trans('Add new image type', [], 'Admin.Design.Feature'),
                'icon' => 'process-icon-new',
            ];
        }

        parent::initPageHeaderToolbar();
    }

    /**
     * Move product images to the new filesystem.
     */
    protected function _moveImagesToNewFileSystem()
    {
        if (!Image::testFileSystem()) {
            $this->errors[] = $this->trans('Error: Your server configuration is not compatible with the new image system. No images were moved.', [], 'Admin.Design.Notification');
        } else {
            ini_set('max_execution_time', $this->max_execution_time); // ini_set may be disabled, we need the real value
            $this->max_execution_time = (int) ini_get('max_execution_time');
            $result = Image::moveToNewFileSystem($this->max_execution_time);
            if ($result === 'timeout') {
                $this->errors[] = $this->trans(
                    'Not all images have been moved. The server timed out before finishing. Click on "%move_images_label%" again to resume the moving process.',
                    [
                        '%move_images_label%' => $this->trans('Move images', [], 'Admin.Design.Feature'),
                    ],
                    'Admin.Design.Notification'
                );
            } elseif ($result === false) {
                $this->errors[] = $this->trans('Error: Some -- or all -- images cannot be moved.', [], 'Admin.Design.Notification');
            }
        }

        return count($this->errors) > 0 ? false : true;
    }

    public function initContent()
    {
        if ($this->display != 'edit' && $this->display != 'add') {
            $this->initRegenerate();

            $this->context->smarty->assign([
                'display_regenerate' => true,
                'display_move' => $this->display_move,
            ]);
        }

        if ($this->display == 'edit') {
            $this->warnings[] = $this->trans('After modification, do not forget to regenerate thumbnails', [], 'Admin.Design.Notification');
            $this->warnings[] = $this->trans('Make sure the theme you use doesn\'t rely on this image format before deleting it.', [], 'Admin.Design.Notification');
        }

        parent::initContent();
    }
}
