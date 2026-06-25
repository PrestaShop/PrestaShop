<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\MailTemplate\Command;

use PrestaShop\PrestaShop\Core\Domain\MailTemplate\ValueObject\EmailTemplateName;
use PrestaShop\PrestaShop\Core\Domain\MailTemplate\ValueObject\EmailTemplateSource;

class EditEmailBodyTemplateCommand
{
    private readonly EmailTemplateName $templateName;
    private readonly EmailTemplateSource $source;

    public function __construct(
        string $templateName,
        private readonly string $locale,
        string $source,
        string $moduleName,
        private readonly string $htmlContent,
        private readonly string $txtContent,
    ) {
        $this->templateName = new EmailTemplateName($templateName);
        $this->source = new EmailTemplateSource($source, $moduleName);
    }

    public function getTemplateName(): EmailTemplateName
    {
        return $this->templateName;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function getSource(): EmailTemplateSource
    {
        return $this->source;
    }

    public function getHtmlContent(): string
    {
        return $this->htmlContent;
    }

    public function getTxtContent(): string
    {
        return $this->txtContent;
    }
}
