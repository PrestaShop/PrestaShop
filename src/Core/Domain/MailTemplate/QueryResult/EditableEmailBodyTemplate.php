<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\MailTemplate\QueryResult;

class EditableEmailBodyTemplate
{
    public function __construct(
        private readonly string $templateName,
        private readonly string $locale,
        private readonly string $source,
        private readonly string $moduleName,
        private readonly ?string $htmlContent,
        private readonly ?string $txtContent,
    ) {
    }

    public function getTemplateName(): string
    {
        return $this->templateName;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function getModuleName(): string
    {
        return $this->moduleName;
    }

    public function getHtmlContent(): ?string
    {
        return $this->htmlContent;
    }

    public function getTxtContent(): ?string
    {
        return $this->txtContent;
    }
}
