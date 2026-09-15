<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\AdminBar;

/**
 * Describes a contextual action displayed in the Front Office Admin Bar.
 *
 * Every action provides an explicit endpoint relative to the Admin Bar Back Office
 * prefix, validated before it is rendered in the Front Office.
 */
final class AdminBarAction
{
    /** @param array<string, int> $parameters */
    public function __construct(
        private readonly string $name,
        private readonly string $label,
        private readonly array $parameters = [],
        private readonly ?string $endpoint = null,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    /** @return array<string, int> */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getEndpoint(): ?string
    {
        return $this->endpoint;
    }
}
