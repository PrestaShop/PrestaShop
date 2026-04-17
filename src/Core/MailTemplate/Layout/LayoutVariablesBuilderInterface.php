<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\MailTemplate\Layout;

use PrestaShop\PrestaShop\Core\Language\LanguageInterface;

/**
 * LayoutVariablesBuilderInterface is used by the MailTemplateRendererInterface
 * to render the mails, it returns an array of generic layout variables like:
 *  - templateModuleName: name of the associated module
 *  - languageIsRTL: is the language read from Right To Left
 *  - locale: the locale in which the template is generated
 *  - emailPublicWebRoot: public mail root for assets
 */
interface LayoutVariablesBuilderInterface
{
    public const BUILD_MAIL_LAYOUT_VARIABLES_HOOK = 'actionBuildMailLayoutVariables';

    /**
     * @param LayoutInterface $layout
     * @param LanguageInterface $language
     *
     * @return array
     */
    public function buildVariables(LayoutInterface $layout, LanguageInterface $language);
}
