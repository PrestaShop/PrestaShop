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
 * Step 4 : configure the shop and admin access
 */
class InstallControllerHttpConfigure extends InstallControllerHttp implements HttpConfigureInterface
{
    /**
     * @var array
     */
    public $list_countries = [];
    /**
     * @var string
     */
    public $install_type;
    /**
     * @var string
     */
    public $translatedStrings;

    /**
     * {@inheritdoc}
     */
    public function processNextStep(): void
    {
        if (Tools::isSubmit('shop_name')) {
            // Save shop configuration
            $this->session->shop_name = trim(Tools::getValue('shop_name'));
            $this->session->enable_ssl = (bool) Tools::getValue('enable_ssl');
            $this->session->shop_country = Tools::getValue('shop_country');
            $this->session->shop_timezone = Tools::getValue('shop_timezone');

            // Save admin configuration
            $this->session->admin_firstname = trim(Tools::getValue('admin_firstname'));
            $this->session->admin_lastname = trim(Tools::getValue('admin_lastname'));
            $this->session->admin_email = trim(Tools::getValue('admin_email'));

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
     * {@inheritdoc}
     */
    public function validate(): bool
    {
        // List of required fields
        $required_fields = ['shop_name', 'shop_country', 'shop_timezone', 'admin_firstname', 'admin_lastname', 'admin_email', 'admin_password'];
        foreach ($required_fields as $field) {
            if (!$this->session->$field) {
                $this->errors[$field] = $this->translator->trans('Field required', [], 'Install');
            }
        }

        // Check shop name
        if ($this->session->shop_name && !Validate::isGenericName($this->session->shop_name)) {
            $this->errors['shop_name'] = $this->translator->trans('Invalid shop name', [], 'Install');
        } elseif (strlen($this->session->shop_name) > 64) {
            $this->errors['shop_name'] = $this->translator->trans('The field %field% is limited to %limit% characters', ['%limit%' => 64, '%field%' => $this->translator->trans('shop name', [], 'Install')], 'Install');
        }

        // Check admin name
        if ($this->session->admin_firstname && !Validate::isName($this->session->admin_firstname)) {
            $this->errors['admin_firstname'] = $this->translator->trans('Your firstname contains some invalid characters', [], 'Install');
        } elseif (strlen($this->session->admin_firstname) > 32) {
            $this->errors['admin_firstname'] = $this->translator->trans('The field %field% is limited to %limit% characters', ['%field%' => $this->translator->trans('firstname', [], 'Install'), '%limit%' => 32], 'Install');
        }

        if ($this->session->admin_lastname && !Validate::isName($this->session->admin_lastname)) {
            $this->errors['admin_lastname'] = $this->translator->trans('Your lastname contains some invalid characters', [], 'Install');
        } elseif (strlen($this->session->admin_lastname) > 32) {
            $this->errors['admin_lastname'] = $this->translator->trans('The field %field% is limited to %limit% characters', ['%field%' => $this->translator->trans('lastname', [], 'Install'), '%limit%' => 32], 'Install');
        }

        // Check passwords
        if ($this->session->admin_password) {
            if (!Validate::isAcceptablePasswordLength($this->session->admin_password)) {
                $this->errors['admin_password'] = $this->translator->trans('The password is incorrect (must be alphanumeric string with at least 8 characters)', [], 'Install');
            } elseif (!Validate::isAcceptablePasswordScore($this->session->admin_password)) {
                $this->errors['admin_password'] = $this->translator->trans('The password is incorrect (must be Strong)', [], 'Install');
            } elseif ($this->session->admin_password != $this->session->admin_password_confirm) {
                $this->errors['admin_password'] = $this->translator->trans('The password and its confirmation are different', [], 'Install');
            }
        }

        // Check email
        if ($this->session->admin_email && !Validate::isEmail($this->session->admin_email)) {
            $this->errors['admin_email'] = $this->translator->trans('This e-mail address is invalid', [], 'Install');
        }

        return count($this->errors) ? false : true;
    }

    /**
     * {@inheritdoc}
     */
    public function process(): void
    {
        if (Tools::getValue('timezoneByIso')) {
            $this->processTimezoneByIso();
        }
    }

    /**
     * Obtain the timezone associated to an iso
     */
    public function processTimezoneByIso(): void
    {
        $timezone = $this->getTimezoneByIso(Tools::getValue('iso'));
        $this->ajaxJsonAnswer((bool) $timezone, $timezone);
    }

    /**
     * Get list of timezones
     *
     * @return array
     */
    public function getTimezones(): array
    {
        if (!file_exists(_PS_INSTALL_DATA_PATH_ . 'xml/timezone.xml')) {
            return [];
        }

        $xml = @simplexml_load_file(_PS_INSTALL_DATA_PATH_ . 'xml/timezone.xml');
        $timezones = [];
        if ($xml) {
            foreach ($xml->entities->timezone as $timezone) {
                $timezones[] = (string) $timezone['name'];
            }
        }

        return $timezones;
    }

    /**
     * Get a timezone associated to an iso
     *
     * @param string $iso
     *
     * @return string
     */
    public function getTimezoneByIso($iso): string
    {
        if (!file_exists(_PS_INSTALL_DATA_PATH_ . 'iso_to_timezone.xml')) {
            return '';
        }

        $xml = @simplexml_load_file(_PS_INSTALL_DATA_PATH_ . 'iso_to_timezone.xml');
        $timezones = [];
        if ($xml) {
            foreach ($xml->relation as $relation) {
                $timezones[(string) $relation['iso']] = (string) $relation['zone'];
            }
        }

        return isset($timezones[$iso]) ? $timezones[$iso] : '';
    }

    /**
     * {@inheritdoc}
     */
    public function display(): void
    {
        // Countries list
        $this->list_countries = [];
        $countries = $this->language->getCountries();
        $top_countries = [
            'fr', 'es', 'us',
            'gb', 'it', 'de',
            'nl', 'pl', 'id',
            'be', 'br', 'se',
            'ca', 'ru', 'cn',
        ];

        foreach ($top_countries as $iso) {
            $this->list_countries[] = ['iso' => $iso, 'name' => $countries[$iso]];
        }
        $this->list_countries[] = ['iso' => 0, 'name' => '-----------------'];

        foreach ($countries as $iso => $lang) {
            if (!in_array($iso, $top_countries)) {
                $this->list_countries[] = ['iso' => $iso, 'name' => $lang];
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

        $this->translatedStrings = json_encode([
            'Straight rows of keys are easy to guess' => $this->translator->trans('Straight rows of keys are easy to guess'),
            'Short keyboard patterns are easy to guess' => $this->translator->trans('Short keyboard patterns are easy to guess'),
            'Use a longer keyboard pattern with more turns' => $this->translator->trans('Use a longer keyboard pattern with more turns'),
            'Repeats like "aaa" are easy to guess' => $this->translator->trans('Repeats like "aaa" are easy to guess'),
            'Repeats like "abcabcabc" are only slightly harder to guess than "abc"' => $this->translator->trans('Repeats like "abcabcabc" are only slightly harder to guess than "abc"'),
            'Sequences like abc or 6543 are easy to guess' => $this->translator->trans('Sequences like "abc" or "6543" are easy to guess'),
            'Recent years are easy to guess' => $this->translator->trans('Recent years are easy to guess'),
            'Dates are often easy to guess' => $this->translator->trans('Dates are often easy to guess'),
            'This is a top-10 common password' => $this->translator->trans('This is a top-10 common password'),
            'This is a top-100 common password' => $this->translator->trans('This is a top-100 common password'),
            'This is a very common password' => $this->translator->trans('This is a very common password'),
            'This is similar to a commonly used password' => $this->translator->trans('This is similar to a commonly used password'),
            'A word by itself is easy to guess' => $this->translator->trans('A word by itself is easy to guess'),
            'Names and surnames by themselves are easy to guess' => $this->translator->trans('Names and surnames by themselves are easy to guess'),
            'Common names and surnames are easy to guess' => $this->translator->trans('Common names and surnames are easy to guess'),
            0 => $this->translator->trans('Very weak'),
            1 => $this->translator->trans('Weak'),
            2 => $this->translator->trans('Average'),
            3 => $this->translator->trans('Strong'),
            4 => $this->translator->trans('Very strong'),
            'Use a few words, avoid common phrases' => $this->translator->trans('Use a few words, avoid common phrases'),
            'No need for symbols, digits, or uppercase letters' => $this->translator->trans('No need for symbols, digits, or uppercase letters'),
            'Avoid repeated words and characters' => $this->translator->trans('Avoid repeated words and characters'),
            'Avoid sequences' => $this->translator->trans('Avoid sequences'),
            'Avoid recent years' => $this->translator->trans('Avoid recent years'),
            'Avoid years that are associated with you' => $this->translator->trans('Avoid years that are associated with you'),
            'Avoid dates and years that are associated with you' => $this->translator->trans('Avoid dates and years that are associated with you'),
            'Capitalization doesn\'t help very much' => $this->translator->trans('Capitalization doesn\'t help very much'),
            'All-uppercase is almost as easy to guess as all-lowercase' => $this->translator->trans('All-uppercase is almost as easy to guess as all-lowercase'),
            'Reversed words aren\'t much harder to guess' => $this->translator->trans('Reversed words aren\'t much harder to guess'),
            'Predictable substitutions like \'@\' instead of \'a\' don\'t help very much' => $this->translator->trans('Predictable substitutions like "@" instead of "a" don\'t help very much'),
            'Add another word or two. Uncommon words are better.' => $this->translator->trans('Add another word or two. Uncommon words are better.'),
        ]);

        $this->displayContent('configure');
    }

    /**
     * Helper to display error for a field
     *
     * @param string $field
     *
     * @return string|null
     */
    public function displayError(string $field): ?string
    {
        if (!isset($this->errors[$field])) {
            return null;
        }

        return '<span class="result aligned errorTxt">' . Tools::htmlentitiesUTF8($this->errors[$field]) . '</span>';
    }
}
