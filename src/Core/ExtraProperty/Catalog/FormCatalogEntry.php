<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Catalog;

use JsonSerializable;

/**
 * One identifiable-object back-office form, as exposed by the form catalog.
 */
final class FormCatalogEntry implements JsonSerializable
{
    /**
     * @param string $id Form id (the form type block prefix)
     * @param string $label Human-readable label
     * @param class-string $formTypeClass Form type FQCN — server-side only, never serialized
     */
    public function __construct(
        public readonly string $id,
        public readonly string $label,
        public readonly string $formTypeClass,
    ) {
    }

    /**
     * The form type class is a server-side implementation detail and is intentionally
     * NOT exposed to the client.
     *
     * @return array{id: string, label: string}
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'label' => $this->label,
        ];
    }
}
