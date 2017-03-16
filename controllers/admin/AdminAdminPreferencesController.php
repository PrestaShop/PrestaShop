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
 * @property Configuration $object
 */
class AdminAdminPreferencesControllerCore extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->className = 'Configuration';
        $this->table = 'configuration';

        parent::__construct();

        // Upload quota
        $max_upload = (int)ini_get('upload_max_filesize');
        $max_post = (int)ini_get('post_max_size');
        $upload_mb = min($max_upload, $max_post);

        // Options list
        $this->fields_options = array(
            'general' => array(
                'title' =>    $this->trans('General', array(), 'Admin.Global'),
                'icon' =>    'icon-cogs',
                'fields' =>    array(
                    'PRESTASTORE_LIVE' => array(
                        'title' => $this->trans('Automatically check for module updates', array(), 'Admin.Advparameters.Feature'),
                        'hint' => $this->trans('New modules and updates are displayed on the modules page.', array(), 'Admin.Advparameters.Help'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'type' => 'bool',
                        'visibility' => Shop::CONTEXT_ALL
                    ),
                    'PS_COOKIE_CHECKIP' => array(
                        'title' => $this->trans('Check the cookie\'s IP address', array(), 'Admin.Advparameters.Feature'),
                        'hint' => $this->trans('Check the IP address of the cookie in order to prevent your cookie from being stolen.', array(), 'Admin.Advparameters.Help'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'type' => 'bool',
                        'default' => '0',
                        'visibility' => Shop::CONTEXT_ALL
                    ),
                    'PS_COOKIE_LIFETIME_FO' => array(
                        'title' => $this->trans('Lifetime of front office cookies', array(), 'Admin.Advparameters.Feature'),
                        'hint' => $this->trans('Set the amount of hours during which the front office cookies are valid. After that amount of time, the customer will have to log in again.', array(), 'Admin.Advparameters.Feature'),
                        'validation' => 'isInt',
                        'cast' => 'intval',
                        'type' => 'text',
                        'suffix' => $this->trans('hours', array(), 'Admin.Advparameters.Feature'),
                        'default' => '480',
                        'visibility' => Shop::CONTEXT_ALL
                    ),
                    'PS_COOKIE_LIFETIME_BO' => array(
                        'title' => $this->trans('Lifetime of back office cookies', array(), 'Admin.Advparameters.Feature'),
                        'hint' => $this->trans('Set the amount of hours during which the back office cookies are valid. After that amount of time, the PrestaShop user will have to log in again.', array(), 'Admin.Advparameters.Help'),
                        'validation' => 'isInt',
                        'cast' => 'intval',
                        'type' => 'text',
                        'suffix' => $this->trans('hours', array(), 'Admin.Advparameters.Feature'),
                        'default' => '480',
                        'visibility' => Shop::CONTEXT_ALL
                    ),
                ),
                'submit' => array('title' => $this->trans('Save', array(), 'Admin.Actions'))
            ),
            'upload' => array(
                'title' =>    $this->trans('Upload quota', array(), 'Admin.Advparameters.Feature'),
                'icon' =>    'icon-cloud-upload',
                'fields' => array(
                    'PS_ATTACHMENT_MAXIMUM_SIZE' => array(
                        'title' => $this->trans('Maximum size for attached files', array(), 'Admin.Advparameters.Feature'),
                        'hint' =>  sprintf($this->trans('Set the maximum size allowed for attachment files (in megabytes). This value has to be lower or equal to the maximum file upload allotted by your server (currently: %s MB).', array(), 'Admin.Advparameters.Help'), $upload_mb),
                        'validation' => 'isInt',
                        'cast' => 'intval',
                        'type' => 'text',
                        'suffix' => $this->trans('megabytes', array(), 'Admin.Advparameters.Feature'),
                        'default' => '2'
                    ),
                    'PS_LIMIT_UPLOAD_FILE_VALUE' => array(
                        'title' => $this->trans('Maximum size for a downloadable product', array(), 'Admin.Advparameters.Feature'),
                        'hint' => sprintf($this->trans('Define the upload limit for a downloadable product (in megabytes). This value has to be lower or equal to the maximum file upload allotted by your server (currently: %s MB).', array(), 'Admin.Advparameters.Help'), $upload_mb),
                        'validation' => 'isInt',
                        'cast' => 'intval',
                        'type' => 'text',
                        'suffix' => $this->trans('megabytes', array(), 'Admin.Advparameters.Feature'),
                        'default' => '1'
                    ),
                    'PS_LIMIT_UPLOAD_IMAGE_VALUE' => array(
                        'title' => $this->trans('Maximum size for a product\'s image', array(), 'Admin.Advparameters.Feature'),
                        'hint' => sprintf($this->trans('Define the upload limit for an image (in megabytes). This value has to be lower or equal to the maximum file upload allotted by your server (currently: %s MB).', array('%s' => $upload_mb), 'Admin.Advparameters.Help')),
                        'validation' => 'isInt',
                        'cast' => 'intval',
                        'type' => 'text',
                        'suffix' => $this->trans('megabytes', array(), 'Admin.Advparameters.Feature'),
                        'default' => '1'
                    ),
                ),
                'submit' => array('title' => $this->trans('Save', array(), 'Admin.Actions'))
            ),
            'notifications' => array(
                'title' =>    $this->trans('Notifications', array(), 'Admin.Advparameters.Feature'),
                'icon' =>    'icon-list-alt',
                'description' => $this->trans('Notifications are numbered bubbles displayed at the very top of your back office, right next to the shop\'s name. They display the number of new items since you last clicked on them.', array(), 'Admin.Advparameters.Help'),
                'fields' =>    array(
                    'PS_SHOW_NEW_ORDERS' => array(
                        'title' => $this->trans('Show notifications for new orders', array(), 'Admin.Advparameters.Feature'),
                        'hint' => $this->trans('This will display notifications when new orders are made in your shop.', array(), 'Admin.Advparameters.Help'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'type' => 'bool'
                    ),
                    'PS_SHOW_NEW_CUSTOMERS' => array(
                        'title' => $this->trans('Show notifications for new customers', array(), 'Admin.Advparameters.Feature'),
                        'hint' => $this->trans('This will display notifications every time a new customer registers in your shop.', array(), 'Admin.Advparameters.Help'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'type' => 'bool'
                    ),
                    'PS_SHOW_NEW_MESSAGES' => array(
                        'title' => $this->trans('Show notifications for new messages', array(), 'Admin.Advparameters.Feature'),
                        'hint' => $this->trans('This will display notifications when new messages are posted in your shop.', array(), 'Admin.Advparameters.Help'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'type' => 'bool'
                    ),
                ),
                'submit' => array('title' => $this->trans('Save', array(), 'Admin.Actions'))
            ),
        );
    }

    public function postProcess()
    {
        $upload_max_size = (int)str_replace('M', '', ini_get('upload_max_filesize'));
        $post_max_size = (int)str_replace('M', '', ini_get('post_max_size'));
        $max_size = $upload_max_size < $post_max_size ? $upload_max_size : $post_max_size;

        if (Tools::getValue('PS_LIMIT_UPLOAD_FILE_VALUE') > $max_size || Tools::getValue('PS_LIMIT_UPLOAD_IMAGE_VALUE') > $max_size) {
            $this->errors[] = $this->trans('The limit chosen is larger than the server\'s maximum upload limit. Please increase the limits of your server.', array(), 'Admin.Advparameters.Notification');
            return;
        }

        if (Tools::getIsset('PS_LIMIT_UPLOAD_FILE_VALUE') && !Tools::getValue('PS_LIMIT_UPLOAD_FILE_VALUE')) {
            $_POST['PS_LIMIT_UPLOAD_FILE_VALUE'] = 1;
        }

        if (Tools::getIsset('PS_LIMIT_UPLOAD_IMAGE_VALUE') && !Tools::getValue('PS_LIMIT_UPLOAD_IMAGE_VALUE')) {
            $_POST['PS_LIMIT_UPLOAD_IMAGE_VALUE'] = 1;
        }

        parent::postProcess();
    }

    /**
     * Update PS_ATTACHMENT_MAXIMUM_SIZE
     *
     * @param $value
     */
    public function updateOptionPsAttachementMaximumSize($value)
    {
        if (!$value) {
            return;
        }

        $upload_max_size = (int)str_replace('M', '', ini_get('upload_max_filesize'));
        $post_max_size = (int)str_replace('M', '', ini_get('post_max_size'));
        $max_size = $upload_max_size < $post_max_size ? $upload_max_size : $post_max_size;
        $value = ($max_size < Tools::getValue('PS_ATTACHMENT_MAXIMUM_SIZE')) ? $max_size : Tools::getValue('PS_ATTACHMENT_MAXIMUM_SIZE');
        Configuration::updateValue('PS_ATTACHMENT_MAXIMUM_SIZE', $value);
    }
}
