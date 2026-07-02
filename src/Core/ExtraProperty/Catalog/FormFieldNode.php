<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Catalog;

use JsonSerializable;

/**
 * One node of a form field tree: a single field of a back-office form, with its
 * children when the field is compound (sub-form, collection prototype root, ...).
 */
final class FormFieldNode implements JsonSerializable
{
    /**
     * @param string $name Field name inside its parent form
     * @param string $path Dot-joined path from the form root (e.g. "address.street")
     * @param string|null $label Resolved label, or a humanized fallback
     * @param string $typeClass FQCN of the field's inner form type
     * @param bool $compound Whether the field is compound (may have children)
     * @param list<FormFieldNode> $children
     */
    public function __construct(
        public readonly string $name,
        public readonly string $path,
        public readonly ?string $label,
        public readonly string $typeClass,
        public readonly bool $compound,
        public readonly array $children,
    ) {
    }

    /**
     * @return array{name: string, path: string, label: ?string, typeClass: string, compound: bool, children: list<FormFieldNode>}
     */
    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
            'path' => $this->path,
            'label' => $this->label,
            'typeClass' => $this->typeClass,
            'compound' => $this->compound,
            'children' => $this->children,
        ];
    }
}
