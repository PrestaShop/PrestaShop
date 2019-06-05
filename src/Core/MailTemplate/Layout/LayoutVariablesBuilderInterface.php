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
    const BUILD_MAIL_LAYOUT_VARIABLES_HOOK = 'actionBuildMailLayoutVariables';

    /**
     * @param LayoutInterface $layout
     * @param LanguageInterface $language
     *
     * @return array
     */
    public function buildVariables(LayoutInterface $layout, LanguageInterface $language);
}
