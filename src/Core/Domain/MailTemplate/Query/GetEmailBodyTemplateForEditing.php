<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\MailTemplate\Query;

use PrestaShop\PrestaShop\Core\Domain\MailTemplate\ValueObject\EmailTemplateName;
use PrestaShop\PrestaShop\Core\Domain\MailTemplate\ValueObject\EmailTemplateSource;

class GetEmailBodyTemplateForEditing
{
    private readonly EmailTemplateName $templateName;
    private readonly EmailTemplateSource $source;

    public function __construct(
        string $templateName,
        private readonly string $locale,
        string $source,
        string $moduleName = '',
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
}
