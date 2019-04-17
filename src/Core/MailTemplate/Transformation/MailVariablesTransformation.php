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

namespace PrestaShop\PrestaShop\Core\MailTemplate\Transformation;

/**
 * Class MailVariablesTransformation is used only for preview, it replaces the
 * variables present in the mail templates (this replacement is usually performed
 * by the Mail class in real behavior).
 * You can set the variables using the actionBuildMailLayoutVariables and setting
 * them in the `templateVars` key.
 */
class MailVariablesTransformation extends AbstractTransformation
{
    /**
     * {@inheritdoc}
     */
    public function apply($templateContent, array $templateVariables)
    {
        if (!empty($templateVariables['templateVars'])) {
            $templateContent = strtr($templateContent, $templateVariables['templateVars']);
        }

        return $templateContent;
    }
}
