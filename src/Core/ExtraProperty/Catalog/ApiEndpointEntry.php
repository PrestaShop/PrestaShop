<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Catalog;

use JsonSerializable;

/**
 * One Admin API endpoint (a URI template and the HTTP methods it accepts), as exposed
 * by the API endpoint catalog.
 */
final class ApiEndpointEntry implements JsonSerializable
{
    /**
     * Source value for endpoints declared by the core (matches ExtraPropertyDefinition::CORE_MODULE_KEY).
     */
    public const SOURCE_CORE = '_core';

    /**
     * @param string $uriTemplate Normalized URI template (single leading slash, no trailing slash)
     * @param list<string> $methods HTTP methods accepted on this URI template
     * @param string $source '_core' for core endpoints, otherwise the module technical name
     */
    public function __construct(
        public readonly string $uriTemplate,
        public readonly array $methods,
        public readonly string $source,
    ) {
    }

    /**
     * @return array{uriTemplate: string, methods: list<string>, source: string}
     */
    public function jsonSerialize(): array
    {
        return [
            'uriTemplate' => $this->uriTemplate,
            'methods' => $this->methods,
            'source' => $this->source,
        ];
    }
}
