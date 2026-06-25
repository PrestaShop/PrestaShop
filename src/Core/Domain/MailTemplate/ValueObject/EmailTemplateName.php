<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\MailTemplate\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\MailTemplate\Exception\EmailTemplateConstraintException;

class EmailTemplateName
{
    private readonly string $name;

    public function __construct(string $name)
    {
        $this->assertIsValid($name);
        $this->name = $name;
    }

    public function getValue(): string
    {
        return $this->name;
    }

    private function assertIsValid(string $name): void
    {
        if (empty($name)) {
            throw new EmailTemplateConstraintException('Email template name cannot be empty.');
        }

        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $name)) {
            throw new EmailTemplateConstraintException(
                sprintf('Email template name "%s" contains invalid characters. Only alphanumeric characters, hyphens, and underscores are allowed.', $name)
            );
        }
    }
}
