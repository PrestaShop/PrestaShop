<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\MailTemplate\Transformation;

use PrestaShop\PrestaShop\Core\MailTemplate\MailTemplateInterface;
use Soundasleep\Html2Text;

/**
 * HTMLTextifyTransformation is used to remove any HTML tags from the template. It
 * is especially useful when no txt layout is defined and the renderer uses the html
 * layout as a base. This transformation then removes any html tags but keep the raw
 * information.
 */
class HTMLToTextTransformation extends AbstractTransformation
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
        $templateContent = Html2Text::convert($templateContent, ['ignore_errors' => true]);
        if (PHP_EOL != $templateContent[strlen($templateContent) - 1]) {
            $templateContent .= PHP_EOL;
        }

        return $templateContent;
    }
}
