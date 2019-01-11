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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\MailTemplate;

use Language;

class MailTemplateParametersBuilder implements MailTemplateParametersBuilderInterface
{
    /** @var array */
    private $defaultParameters;

    /** @var array */
    private $languageDefaultFonts = array(
        'fa' => 'Tahoma',
        'ar' => 'Tahoma',
    );

    /**
     * @param array $defaultParameters
     */
    public function __construct(array $defaultParameters = [])
    {
        $this->defaultParameters = $defaultParameters;
    }

    /**
     * {@inheritdoc}
     */
    public function buildParameters(MailTemplateInterface $template, Language $language)
    {
        $languageDefaultFont = '';
        if (isset($this->languageDefaultFonts[$language->iso_code])) {
            $languageDefaultFont = $this->languageDefaultFonts[$language->iso_code] . ',';
        }

        $parameters = array_merge($this->defaultParameters, [
            'languageIsRTL' => (bool) $language->is_rtl,
            'languageDefaultFont' => $languageDefaultFont,
            'templateTheme' => $template->getTheme(),
            'templateName' => $template->getName(),
            'templateModuleName' => $template->getModuleName(),
            'locale' => $language->locale,
        ]);

        return $parameters;
    }
}
