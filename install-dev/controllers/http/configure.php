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
 * Step 4 : configure the shop and admin access
 */
class InstallControllerHttpConfigure extends InstallControllerHttp implements HttpConfigureInterface
{
    public $list_countries = array();
    public $install_type;

    /**
     * @see HttpConfigureInterface::processNextStep()
     */
    public function processNextStep()
    {
        if (Tools::isSubmit('shop_name')) {

            // Save shop configuration
            $this->session->shop_name = trim(Tools::getValue('shop_name'));
            $this->session->shop_activity = Tools::getValue('shop_activity');
            $this->session->install_type = Tools::getValue('db_mode');
            $this->session->shop_country = Tools::getValue('shop_country');
            $this->session->shop_timezone = Tools::getValue('shop_timezone');

            // Save admin configuration
            $this->session->admin_firstname = trim(Tools::getValue('admin_firstname'));
            $this->session->admin_lastname = trim(Tools::getValue('admin_lastname'));
            $this->session->admin_email = trim(Tools::getValue('admin_email'));
            $this->session->send_informations = Tools::getValue('send_informations');
            if ($this->session->send_informations) {
                $params = http_build_query(array(
                    'email' => $this->session->admin_email,
                    'method' => 'addMemberToNewsletter',
                    'language' => $this->language->getLanguageIso(),
                    'visitorType' => 1,
                    'source' => 'installer'
                ));
                Tools::file_get_contents('http://www.prestashop.com/ajax/controller.php?'.$params);
            }

            // If password fields are empty, but are already stored in session, do not fill them again
            if (!$this->session->admin_password || trim(Tools::getValue('admin_password'))) {
                $this->session->admin_password = trim(Tools::getValue('admin_password'));
            }

            if (!$this->session->admin_password_confirm || trim(Tools::getValue('admin_password_confirm'))) {
                $this->session->admin_password_confirm = trim(Tools::getValue('admin_password_confirm'));
            }
        }
    }

    /**
     * @see HttpConfigureInterface::validate()
     */
    public function validate()
    {
        // List of required fields
        $required_fields = array('shop_name', 'shop_country', 'shop_timezone', 'admin_firstname', 'admin_lastname', 'admin_email', 'admin_password');
        foreach ($required_fields as $field) {
            if (!$this->session->$field) {
                $this->errors[$field] = $this->translator->trans('Field required', array(), 'Install');
            }
        }

        // Check shop name
        if ($this->session->shop_name && !Validate::isGenericName($this->session->shop_name)) {
            $this->errors['shop_name'] = $this->translator->trans('Invalid shop name', array(), 'Install');
        } elseif (strlen($this->session->shop_name) > 64) {
            $this->errors['shop_name'] = $this->translator->trans('The field %field% is limited to %limit% characters', array('%limit%' => 64, '%field%' => $this->translator->trans('shop name', array(), 'Install')), 'Install');
        }

        // Check admin name
        if ($this->session->admin_firstname && !Validate::isName($this->session->admin_firstname)) {
            $this->errors['admin_firstname'] = $this->translator->trans('Your firstname contains some invalid characters', array(), 'Install');
        } elseif (strlen($this->session->admin_firstname) > 32) {
            $this->errors['admin_firstname'] = $this->translator->trans('The field %field% is limited to %limit% characters', array('%field%' => $this->translator->trans('firstname', array(), 'Install'), '%limit%' => 32), 'Install');
        }

        if ($this->session->admin_lastname && !Validate::isName($this->session->admin_lastname)) {
            $this->errors['admin_lastname'] = $this->translator->trans('Your lastname contains some invalid characters', array(), 'Install');
        } elseif (strlen($this->session->admin_lastname) > 32) {
            $this->errors['admin_lastname'] = $this->translator->trans('The field %field% is limited to %limit% characters', array('%field%' => $this->translator->trans('lastname', array(), 'Install'), '%limit%' => 32), 'Install');
        }

        // Check passwords
        if ($this->session->admin_password) {
            if (!Validate::isPasswdAdmin($this->session->admin_password)) {
                $this->errors['admin_password'] = $this->translator->trans('The password is incorrect (must be alphanumeric string with at least 8 characters)', array(), 'Install');
            } elseif ($this->session->admin_password != $this->session->admin_password_confirm) {
                $this->errors['admin_password'] = $this->translator->trans('The password and its confirmation are different', array(), 'Install');
            }
        }

        // Check email
        if ($this->session->admin_email && !Validate::isEmail($this->session->admin_email)) {
            $this->errors['admin_email'] = $this->translator->trans('This e-mail address is invalid', array(), 'Install');
        }

        return count($this->errors) ? false : true;
    }

    public function process()
    {
        if (Tools::getValue('uploadLogo')) {
            $this->processUploadLogo();
        } elseif (Tools::getValue('timezoneByIso')) {
            $this->processTimezoneByIso();
        }
    }

    /**
     * Process the upload of new logo
     */
    public function processUploadLogo()
    {
        $error = '';
        if (isset($_FILES['fileToUpload']['tmp_name']) && $_FILES['fileToUpload']['tmp_name']) {
            $file = $_FILES['fileToUpload'];
            $error = ImageManager::validateUpload($file, 300000);
            if (!strlen($error)) {
                $tmp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');
                if (!$tmp_name || !move_uploaded_file($file['tmp_name'], $tmp_name)) {
                    return false;
                }

                list($width, $height, $type) = getimagesize($tmp_name);

                $newheight = ($height > 500) ? 500 : $height;
                $percent = $newheight / $height;
                $newwidth = $width * $percent;
                $newheight = $height * $percent;

                if (!is_writable(_PS_ROOT_DIR_.'/img/')) {
                    $error = $this->translator->trans('Image folder %s is not writable', array(_PS_ROOT_DIR_.'/img/'), 'Install');
                }
                if (!$error) {
                    list($src_width, $src_height, $type) = getimagesize($tmp_name);
                    $src_image = ImageManager::create($type, $tmp_name);
                    $dest_image = imagecreatetruecolor($src_width, $src_height);
                    $white = imagecolorallocate($dest_image, 255, 255, 255);
                    imagefilledrectangle($dest_image, 0, 0, $src_width, $src_height, $white);
                    imagecopyresampled($dest_image, $src_image, 0, 0, 0, 0, $src_width, $src_height, $src_width, $src_height);
                    if (!imagejpeg($dest_image, _PS_ROOT_DIR_.'/img/logo.jpg', 95)) {
                        $error = $this->trans('An error occurred during logo copy.', array(), 'Install');
                    } else {
                        imagedestroy($dest_image);
                        @chmod($filename, 0664);
                    }
                }
            } else {
                $error = $this->translator->trans('An error occurred during logo upload.', array(), 'Install');
            }
        }

        $this->ajaxJsonAnswer(($error) ? false : true, $error);
    }

    /**
     * Obtain the timezone associated to an iso
     */
    public function processTimezoneByIso()
    {
        $timezone = $this->getTimezoneByIso(Tools::getValue('iso'));
        $this->ajaxJsonAnswer(($timezone) ? true : false, $timezone);
    }

    /**
     * Get list of timezones
     *
     * @return array
     */
    public function getTimezones()
    {
        if (!is_null($this->cache_timezones)) {
            return;
        }

        if (!file_exists(_PS_INSTALL_DATA_PATH_.'xml/timezone.xml')) {
            return array();
        }

        $xml = @simplexml_load_file(_PS_INSTALL_DATA_PATH_.'xml/timezone.xml');
        $timezones = array();
        if ($xml) {
            foreach ($xml->entities->timezone as $timezone) {
                $timezones[] = (string)$timezone['name'];
            }
        }
        return $timezones;
    }

    /**
     * Get a timezone associated to an iso
     *
     * @param string $iso
     * @return string
     */
    public function getTimezoneByIso($iso)
    {
        if (!file_exists(_PS_INSTALL_DATA_PATH_.'iso_to_timezone.xml')) {
            return '';
        }

        $xml = @simplexml_load_file(_PS_INSTALL_DATA_PATH_.'iso_to_timezone.xml');
        $timezones = array();
        if ($xml) {
            foreach ($xml->relation as $relation) {
                $timezones[(string)$relation['iso']] = (string)$relation['zone'];
            }
        }
        return isset($timezones[$iso]) ? $timezones[$iso] : '';
    }

    /**
     * @see HttpConfigureInterface::display()
     */
    public function display()
    {
        // List of activities
        $list_activities = array(
            1 => $this->translator->trans('Lingerie and Adult', array(), 'Install'),
            2 => $this->translator->trans('Animals and Pets', array(), 'Install'),
            3 => $this->translator->trans('Art and Culture', array(), 'Install'),
            4 => $this->translator->trans('Babies', array(), 'Install'),
            5 => $this->translator->trans('Beauty and Personal Care', array(), 'Install'),
            6 => $this->translator->trans('Cars', array(), 'Install'),
            7 => $this->translator->trans('Computer Hardware and Software', array(), 'Install'),
            8 => $this->translator->trans('Download', array(), 'Install'),
            9 => $this->translator->trans('Fashion and accessories', array(), 'Install'),
            10 => $this->translator->trans('Flowers, Gifts and Crafts', array(), 'Install'),
            11 => $this->translator->trans('Food and beverage', array(), 'Install'),
            12 => $this->translator->trans('HiFi, Photo and Video', array(), 'Install'),
            13 => $this->translator->trans('Home and Garden', array(), 'Install'),
            14 => $this->translator->trans('Home Appliances', array(), 'Install'),
            15 => $this->translator->trans('Jewelry', array(), 'Install'),
            16 => $this->translator->trans('Mobile and Telecom', array(), 'Install'),
            17 => $this->translator->trans('Services', array(), 'Install'),
            18 => $this->translator->trans('Shoes and accessories', array(), 'Install'),
            19 => $this->translator->trans('Sports and Entertainment', array(), 'Install'),
            20 => $this->translator->trans('Travel', array(), 'Install'),
        );

        asort($list_activities);
        $this->list_activities = $list_activities;

        // Countries list
        $this->list_countries = array();
        $countries = $this->language->getCountries();
        $top_countries = array(
            'fr', 'es', 'us',
            'gb', 'it', 'de',
            'nl', 'pl', 'id',
            'be', 'br', 'se',
            'ca', 'ru', 'cn',
        );

        foreach ($top_countries as $iso) {
            $this->list_countries[] = array('iso' => $iso, 'name' => $countries[$iso]);
        }
        $this->list_countries[] = array('iso' => 0, 'name' => '-----------------');

        foreach ($countries as $iso => $lang) {
            if (!in_array($iso, $top_countries)) {
                $this->list_countries[] = array('iso' => $iso, 'name' => $lang);
            }
        }

        // Try to detect default country
        if (!$this->session->shop_country) {
            $detect_language = $this->language->detectLanguage();
            if (isset($detect_language['primarytag'])) {
                $this->session->shop_country = strtolower(isset($detect_language['subtag']) ? $detect_language['subtag'] : $detect_language['primarytag']);
                $this->session->shop_timezone = $this->getTimezoneByIso($this->session->shop_country);
            }
        }

        // Install type
        $this->install_type = ($this->session->install_type) ? $this->session->install_type : 'full';

        $this->displayTemplate('configure');
    }

    /**
     * Helper to display error for a field
     *
     * @param string $field
     * @return string|void
     */
    public function displayError($field)
    {
        if (!isset($this->errors[$field])) {
            return;
        }

        return '<span class="result aligned errorTxt">'.$this->errors[$field].'</span>';
    }
}
