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

use PrestaShop\PrestaShop\Core\MailTemplate\MailTemplateInterface;
use Html2Text\Html2Text;

/**
 * HTMLTextifyTransformation is used to remove any HTML tags from the template. It
 * is especially useful when no txt layout is defined and the renderer uses the html
 * layout as a base. This transformation then removes any html tags but keep the raw
 * information.
 */
class HTMLTextifyTransformation extends AbstractTransformation
{
    public function __construct()
    {
        parent::__construct(MailTemplateInterface::TXT_TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function apply($templateContent, array $templateVariables)
    {
        $textifier = new Html2Text($templateContent);
        $templateContent = $textifier->getText();

        $templateContent = preg_replace('/^\s+/m', '', $templateContent);
        $templateContent = preg_replace_callback('/\{\w+\}/', function ($m) {
            return strtolower($m[0]);
        }, $templateContent);

        return $templateContent;
    }
}
