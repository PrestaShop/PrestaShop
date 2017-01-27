<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

/**
 * @property Gender $object
 */
class AdminGendersControllerCore extends AdminController
{
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

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->trans('Delete selected', array(), 'Admin.Actions'),
                'confirm' => $this->trans('Delete selected items?', array(), 'Admin.Notifications.Warning'),
                'icon' => 'icon-trash'
            )
        );

        $this->default_image_height = 16;
        $this->default_image_width = 16;

        $this->fieldImageSettings = array(
            'name' => 'image',
            'dir' => 'genders'
        );

        $this->fields_list = array(
            'id_gender' => array(
                'title' => $this->trans('ID', array(), 'Admin.Global'),
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ),
            'name' => array(
                'title' => $this->trans('Social title', array(), 'Admin.Shopparameters.Feature'),
                'filter_key' => 'b!name'
            ),
            'type' => array(
                'title' => $this->trans('Gender', array(), 'Admin.Global'),
                'orderby' => false,
                'type' => 'select',
                'list' => array(
                    0 => $this->trans('Male', array(), 'Admin.Shopparameters.Feature'),
                    1 => $this->trans('Female', array(), 'Admin.Shopparameters.Feature'),
                    2 => $this->trans('Neutral', array(), 'Admin.Shopparameters.Feature')
                ),
                'filter_key' => 'a!type',
                'callback' => 'displayGenderType',
                'callback_object' => $this
            ),
            'image' => array(
                'title' => $this->trans('Image', array(), 'Admin.Global'),
                'align' => 'center',
                'image' => 'genders',
                'orderby' => false,
                'search' => false
            )
        );
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_gender'] = array(
                'href' => self::$currentIndex.'&addgender&token='.$this->token,
                'desc' => $this->trans('Add new social title', array(), 'Admin.Shopparameters.Feature'),
                'icon' => 'process-icon-new'
            );
        }

        parent::initPageHeaderToolbar();
    }

    public function renderForm()
    {
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->trans('Social titles', array(), 'Admin.Shopparameters.Feature'),
                'icon' => 'icon-male'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->trans('Social title', array(), 'Admin.Global'),
                    'name' => 'name',
                    'lang' => true,
                    'col' => 4,
                    'hint' => $this->trans('Invalid characters:', array(), 'Admin.Shopparameters.Help').' 0-9!&lt;&gt;,;?=+()@#"�{}_$%:',
                    'required' => true
                ),
                array(
                    'type' => 'radio',
                    'label' => $this->trans('Gender', array(), 'Admin.Global'),
                    'name' => 'type',
                    'required' => false,
                    'class' => 't',
                    'values' => array(
                        array(
                            'id' => 'type_male',
                            'value' => 0,
                            'label' => $this->trans('Male', array(), 'Admin.Shopparameters.Feature')
                        ),
                        array(
                            'id' => 'type_female',
                            'value' => 1,
                            'label' => $this->trans('Female', array(), 'Admin.Shopparameters.Feature')
                        ),
                        array(
                            'id' => 'type_neutral',
                            'value' => 2,
                            'label' => $this->trans('Neutral', array(), 'Admin.Shopparameters.Feature')
                        )
                    )
                ),
                array(
                    'type' => 'file',
                    'label' => $this->trans('Image', array(), 'Admin.Global'),
                    'name' => 'image',
                    'col' => 6,
                    'value' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('Image width', array(), 'Admin.Shopparameters.Feature'),
                    'name' => 'img_width',
                    'col' => 2,
                    'hint' => $this->trans('Image width in pixels. Enter "0" to use the original size.', array(), 'Admin.Shopparameters.Help')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('Image height', array(), 'Admin.Shopparameters.Feature'),
                    'name' => 'img_height',
                    'col' => 2,
                    'hint' => $this->trans('Image height in pixels. Enter "0" to use the original size.', array(), 'Admin.Shopparameters.Help')
                )
            ),
            'submit' => array(
                'title' => $this->trans('Save', array(), 'Admin.Actions'),
            )
        );

        /** @var Gender $obj */
        if (!($obj = $this->loadObject(true))) {
            return;
        }

        $this->fields_value = array(
            'img_width' => $this->default_image_width,
            'img_height' => $this->default_image_height,
            'image' => $obj->getImage()
        );

        return parent::renderForm();
    }

    public function displayGenderType($value, $tr)
    {
        return $this->fields_list['type']['list'][$value];
    }

    protected function postImage($id)
    {
        if (isset($this->fieldImageSettings['name']) && isset($this->fieldImageSettings['dir'])) {
            if (!Validate::isInt(Tools::getValue('img_width')) || !Validate::isInt(Tools::getValue('img_height'))) {
                $this->errors[] = $this->trans('Width and height must be numeric values.', array(), 'Admin.Shopparameters.Notification');
            } else {
                if ((int)Tools::getValue('img_width') > 0 && (int)Tools::getValue('img_height') > 0) {
                    $width = (int)Tools::getValue('img_width');
                    $height = (int)Tools::getValue('img_height');
                } else {
                    $width = null;
                    $height = null;
                }
                return $this->uploadImage($id, $this->fieldImageSettings['name'], $this->fieldImageSettings['dir'].'/', false, $width, $height);
            }
        }
        return !count($this->errors) ? true : false;
    }

    protected function afterImageUpload()
    {
        parent::afterImageUpload();

        if (($id_gender = (int)Tools::getValue('id_gender')) &&
             isset($_FILES) && count($_FILES) && file_exists(_PS_GENDERS_DIR_.$id_gender.'.jpg')) {
            $current_file = _PS_TMP_IMG_DIR_.'gender_mini_'.$id_gender.'_'.$this->context->shop->id.'.jpg';

            if (file_exists($current_file)) {
                unlink($current_file);
            }
        }

        return true;
    }
}
