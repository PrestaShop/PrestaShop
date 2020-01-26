<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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

namespace PrestaShop\PrestaShop\Adapter\MailTemplate;

use Language;

/**
 * This class will provide data from filesystem about MailTemplate.
 */
class MailTemplateDataProvider
{
    /**
     * @var Language[]
     */
    private $languages;

    public function __construct(array $languages)
    {
        $this->languages = $languages;
    }

    /**
     * Get all templates.
     *
     * @return array Suppliers
     */
    public function getTemplates()
    {
        $mailsTemplates = [];

        foreach ($this->languages as $language) {
            $languageIsoCode = $language['iso_code'];
            $languageId = $language['id_lang'];

            $mailsPath = _PS_ROOT_DIR_ . DIRECTORY_SEPARATOR . 'mails' . DIRECTORY_SEPARATOR . $languageIsoCode;
            // Mail templates can also be found in the theme folder
            $themePath = _PS_THEME_DIR_ . 'mails' . DIRECTORY_SEPARATOR;

            // If there is no folder for the given iso_code in /mails or in /themes/[theme_name]/mails, we bypass this language
            if (!@filemtime($mailsPath) && !@filemtime($themePath . $languageIsoCode)) {
                return [];
            }

            $themeTemplatesDir = $themePath . $languageIsoCode;
            $themeTemplates = is_dir($themeTemplatesDir) ? scandir($themeTemplatesDir, SCANDIR_SORT_NONE) : [];

            // We merge all available emails in one array
            $templates = array_unique(array_merge(scandir($mailsPath, SCANDIR_SORT_NONE), $themeTemplates));
            foreach ($templates as $key => $template) {
                if (!strncmp(strrev($template), 'lmth.', 5)) {
                    $searchResult = array_search($template, $themeTemplates);
                    $mailsTemplates[$languageId][] = [
                        'id' => substr($template, 0, -5),
                        'name' => substr($template, 0, -5),
                        'folder' => ((!empty($searchResult) ? $themePath : _MAIL_DIR_)),
                        'language_code' => $language['language_code'],
                    ];
                }
            }
        }

        return $mailsTemplates;
    }
}
