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

namespace PrestaShop\PrestaShop\Core\MailTemplate\Transformation;

use Html2Text\Html2Text;

class HTMLTextifyTransformation extends AbstractMailTemplateTransformation
{
    /**
     * {@inheritdoc}
     */
    public function apply($templateContent, array $templateVariables)
    {
        $textifier = new Html2Text($templateContent);
        $templateContent = $textifier->getText();

        $templateContent = preg_replace('/^\s+/m', '', $templateContent);
        //$templateContent = preg_replace('/^ +$/m', "", $templateContent);
        $templateContent = preg_replace_callback('/\{\w+\}/', function ($m) {
            return strtolower($m[0]);
        }, $templateContent);

        if (!empty($_SERVER['HTTP_HOST'])) {
            $templateContent = preg_replace('#\w+://' . preg_quote($_SERVER['HTTP_HOST']) . '/?#i', '', $templateContent);
        }

        return $templateContent;
    }
}
