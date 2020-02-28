<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\MailTemplate\Transformation;

use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;

/**
 * Class MailVariablesTransformation is used only for preview, it replaces the
 * variables present in the mail templates (this replacement is usually performed
 * by the Mail class in real behavior).
 * You can set the variables using the actionBuildMailLayoutVariables and setting
 * them in the `templateVars` key, or simply via the constructor.
 */
class MailVariablesTransformation extends AbstractTransformation
{
    /**
     * @var array
     */
    private $replaceVariables;

    /**
     * @param string $type
     * @param array $replaceVariables
     *
     * @throws InvalidArgumentException
     */
    public function __construct($type, array $replaceVariables = [])
    {
        parent::__construct($type);
        $this->replaceVariables = $replaceVariables;
    }

    /**
     * {@inheritdoc}
     */
    public function apply($templateContent, array $templateVariables)
    {
        $replaceVariables = $this->replaceVariables;
        if (!empty($templateVariables['templateVars'])) {
            $replaceVariables = array_merge_recursive(
                $replaceVariables,
                $templateVariables['templateVars']
            );
        }
        if (!empty($replaceVariables)) {
            $templateContent = strtr($templateContent, $replaceVariables);
        }

        return $templateContent;
    }
}
