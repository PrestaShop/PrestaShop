<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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

        // This hook allows to change the variables of a layout
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
