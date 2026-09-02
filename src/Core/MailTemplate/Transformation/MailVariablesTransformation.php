<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
