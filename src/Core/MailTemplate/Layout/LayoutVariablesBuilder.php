<?php
/**
 * 2007-2019 PrestaShop and Contributors
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

namespace PrestaShop\PrestaShop\Core\MailTemplate\Layout;

use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShop\PrestaShop\Core\Language\LanguageDefaultFontsCatalog;
use PrestaShop\PrestaShop\Core\Language\LanguageInterface;

/**
 * MailLayoutVariablesBuilder is a basic implementation of MailLayoutVariablesBuilderInterface
 *  - it formats the variables for template
 *  - it is able to inject default fonts for specified languages
 *  - it includes default variables (set in the constructor)
 *  - it dispatches a hook to allow overriding its output
 */
class LayoutVariablesBuilder implements LayoutVariablesBuilderInterface
{
    /** @var array */
    private $defaultVariables;

    /** @var HookDispatcherInterface */
    private $hookDispatcher;

    /**
     * @var LanguageDefaultFontsCatalog
     */
    private $languageDefaultFonts;

    /**
     * @param HookDispatcherInterface $hookDispatcher
     * @param LanguageDefaultFontsCatalog $languageDefaultFonts
     * @param array $defaultVariables
     */
    public function __construct(
        HookDispatcherInterface $hookDispatcher,
        LanguageDefaultFontsCatalog $languageDefaultFonts,
        array $defaultVariables = []
    ) {
        $this->hookDispatcher = $hookDispatcher;
        $this->languageDefaultFonts = $languageDefaultFonts;
        $this->defaultVariables = $defaultVariables;
    }

    /**
     * {@inheritdoc}
     */
    public function buildVariables(LayoutInterface $mailLayout, LanguageInterface $language)
    {
        $languageDefaultFont = $this->languageDefaultFonts->getDefaultFontByLanguage($language);
        if (!empty($languageDefaultFont)) {
            $languageDefaultFont .= ',';
        }

        $mailLayoutVariables = array_merge($this->defaultVariables, [
            'languageIsRTL' => $language->isRTL(),
            'languageDefaultFont' => $languageDefaultFont,
            'templateName' => $mailLayout->getName(),
            'templateModuleName' => $mailLayout->getModuleName(),
            'locale' => $language->getLocale(),
        ]);

        //This hook allows to change the variables of a layout
        $this->hookDispatcher->dispatchWithParameters(
            LayoutVariablesBuilderInterface::BUILD_MAIL_LAYOUT_VARIABLES_HOOK,
            [
                'mailLayout' => $mailLayout,
                'mailLayoutVariables' => &$mailLayoutVariables,
            ]
        );

        return $mailLayoutVariables;
    }
}
