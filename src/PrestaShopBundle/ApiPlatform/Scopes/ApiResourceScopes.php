<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\ApiPlatform\Scopes;

/**
 * @internal
 */
class ApiResourceScopes
{
    public static function createModuleScopes(
        array $scopes,
        string $moduleName
    ) {
        return new self($scopes, $moduleName);
    }

    public static function createCoreScopes(
        array $scopes
    ) {
        return new self($scopes, null);
    }

    private function __construct(
        private array $scopes,
        private ?string $moduleName
    ) {
    }

    /**
     * List of scopes.
     *
     * @return string[]
     */
    public function getScopes(): array
    {
        return $this->scopes;
    }

    public function fromCore(): bool
    {
        return $this->moduleName === null;
    }

    public function getModuleName(): ?string
    {
        return $this->moduleName;
    }
}
