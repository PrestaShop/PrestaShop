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
 * @property Gender $object
 */
class AdminGendersControllerCore extends AdminController
{
    /**
     * @var int
     */
    public $default_image_height;

    /**
     * @var int
     */
    public $default_image_width;

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'gender';
        $this->className = 'Gender';
        $this->lang = true;
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        parent::__construct();

        if (!Tools::getValue('realedit')) {
            $this->deleted = false;
        }

        $this->bulk_actions = [
            'delete' => [
                'text' => $this->trans('Delete selected', [], 'Admin.Actions'),
                'confirm' => $this->trans('Delete selected items?', [], 'Admin.Notifications.Warning'),
                'icon' => 'icon-trash',
            ],
        ];

        $this->default_image_height = 16;
        $this->default_image_width = 16;

        $this->fieldImageSettings = [
            'name' => 'image',
            'dir' => 'genders',
        ];

        $this->fields_list = [
            'id_gender' => [
                'title' => $this->trans('ID', [], 'Admin.Global'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ],
            'name' => [
                'title' => $this->trans('Social title', [], 'Admin.Shopparameters.Feature'),
                'filter_key' => 'b!name',
            ],
            'type' => [
                'title' => $this->trans('Gender', [], 'Admin.Global'),
                'orderby' => false,
                'type' => 'select',
                'list' => [
                    0 => $this->trans('Male', [], 'Admin.Shopparameters.Feature'),
                    1 => $this->trans('Female', [], 'Admin.Shopparameters.Feature'),
                    2 => $this->trans('Neutral', [], 'Admin.Shopparameters.Feature'),
                ],
                'filter_key' => 'a!type',
                'callback' => 'displayGenderType',
                'callback_object' => $this,
            ],
            'image' => [
                'title' => $this->trans('Image', [], 'Admin.Global'),
                'align' => 'center',
                'image' => 'genders',
                'orderby' => false,
                'search' => false,
            ],
        ];
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_gender'] = [
                'href' => self::$currentIndex . '&addgender&token=' . $this->token,
                'desc' => $this->trans('Add new social title', [], 'Admin.Shopparameters.Feature'),
                'icon' => 'process-icon-new',
            ];
        }

        parent::initPageHeaderToolbar();
    }

    public function renderForm()
    {
        $this->fields_form = [
            'legend' => [
                'title' => $this->trans('Social titles', [], 'Admin.Shopparameters.Feature'),
                'icon' => 'icon-male',
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->trans('Social title', [], 'Admin.Global'),
                    'name' => 'name',
                    'lang' => true,
                    'col' => 4,
                    'hint' => $this->trans('Invalid characters:', [], 'Admin.Shopparameters.Help') . ' 0-9!&lt;&gt;,;?=+()@#"�{}_$%:',
                    'required' => true,
                ],
                [
                    'type' => 'radio',
                    'label' => $this->trans('Gender', [], 'Admin.Global'),
                    'name' => 'type',
                    'required' => false,
                    'class' => 't',
                    'values' => [
                        [
                            'id' => 'type_male',
                            'value' => 0,
                            'label' => $this->trans('Male', [], 'Admin.Shopparameters.Feature'),
                        ],
                        [
                            'id' => 'type_female',
                            'value' => 1,
                            'label' => $this->trans('Female', [], 'Admin.Shopparameters.Feature'),
                        ],
                        [
                            'id' => 'type_neutral',
                            'value' => 2,
                            'label' => $this->trans('Neutral', [], 'Admin.Shopparameters.Feature'),
                        ],
                    ],
                ],
                [
                    'type' => 'file',
                    'label' => $this->trans('Image', [], 'Admin.Global'),
                    'name' => 'image',
                    'col' => 6,
                    'value' => true,
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Image width', [], 'Admin.Shopparameters.Feature'),
                    'name' => 'img_width',
                    'col' => 2,
                    'hint' => $this->trans('Image width in pixels. Enter "0" to use the original size.', [], 'Admin.Shopparameters.Help'),
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Image height', [], 'Admin.Shopparameters.Feature'),
                    'name' => 'img_height',
                    'col' => 2,
                    'hint' => $this->trans('Image height in pixels. Enter "0" to use the original size.', [], 'Admin.Shopparameters.Help'),
                ],
            ],
            'submit' => [
                'title' => $this->trans('Save', [], 'Admin.Actions'),
            ],
        ];

        /** @var Gender|false $obj */
        $obj = $this->loadObject(true);
        if (!$obj) {
            return;
        }

        $this->fields_value = [
            'img_width' => $this->default_image_width,
            'img_height' => $this->default_image_height,
            'image' => $obj->getImage(),
        ];

        return parent::renderForm();
    }

    public function displayGenderType($value, $tr)
    {
        return $this->fields_list['type']['list'][$value];
    }

    protected function postImage($id)
    {
        if (isset($this->fieldImageSettings['name'], $this->fieldImageSettings['dir'])) {
            if (!Validate::isInt(Tools::getValue('img_width')) || !Validate::isInt(Tools::getValue('img_height'))) {
                $this->errors[] = $this->trans('Width and height must be numeric values.', [], 'Admin.Shopparameters.Notification');
            } else {
                if ((int) Tools::getValue('img_width') > 0 && (int) Tools::getValue('img_height') > 0) {
                    $width = (int) Tools::getValue('img_width');
                    $height = (int) Tools::getValue('img_height');
                } else {
                    $width = null;
                    $height = null;
                }

                return $this->uploadImage($id, $this->fieldImageSettings['name'], $this->fieldImageSettings['dir'] . '/', false, $width, $height);
            }
        }

        return !count($this->errors);
    }

    protected function afterImageUpload()
    {
        parent::afterImageUpload();

        if (($id_gender = (int) Tools::getValue('id_gender'))
            && count($_FILES)
            && file_exists(_PS_GENDERS_DIR_ . $id_gender . '.jpg')) {
            $current_file = _PS_TMP_IMG_DIR_ . 'gender_mini_' . $id_gender . '_' . $this->context->shop->id . '.jpg';

            if (file_exists($current_file)) {
                unlink($current_file);
            }
        }

        return true;
    }
}
