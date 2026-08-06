<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\MailTemplate\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\MailTemplate\Exception\EmailTemplateConstraintException;

class EmailTemplateSource
{
    public const SOURCE_CORE = 'core';
    public const SOURCE_MODULE = 'module';

    private readonly string $source;
    private readonly string $moduleName;

    public function __construct(string $source, string $moduleName = '')
    {
        $this->assertIsValid($source, $moduleName);
        $this->source = $source;
        $this->moduleName = $moduleName;
    }

    public static function buildCoreSource(): self
    {
        return new self(self::SOURCE_CORE);
    }

    public static function buildModuleSource(string $moduleName): self
    {
        return new self(self::SOURCE_MODULE, $moduleName);
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function getModuleName(): string
    {
        return $this->moduleName;
    }

    public function isCore(): bool
    {
        return $this->source === self::SOURCE_CORE;
    }

    public function isModule(): bool
    {
        return $this->source === self::SOURCE_MODULE;
    }

    private function assertIsValid(string $source, string $moduleName): void
    {
        if (!in_array($source, [self::SOURCE_CORE, self::SOURCE_MODULE], true)) {
            throw new EmailTemplateConstraintException(
                sprintf('Invalid email template source "%s". Allowed values: %s, %s.', $source, self::SOURCE_CORE, self::SOURCE_MODULE)
            );
        }

        if ($source === self::SOURCE_MODULE && empty($moduleName)) {
            throw new EmailTemplateConstraintException('Module name is required for module email template source.');
        }
    }
}
