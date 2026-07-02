<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Catalog;

use PrestaShop\PrestaShop\Core\ExtraProperty\Form\ExtraPropertyFormTypeMap;
use PrestaShop\PrestaShop\Core\ExtraProperty\Validation\ExtraPropertyConstraintCatalog;

/**
 * Aggregates the JSON-ready payloads the extra property definition form inlines into the page
 * (one <script type="application/json"> block per card), so the picker components can suggest
 * forms, grids, API endpoints, constraints and the effective default form type without AJAX.
 *
 * The form field tree is the deliberate exception: it is expensive to compute per form, so it is
 * served lazily by the dedicated /form-fields/{formId} endpoint (FormFieldTreeProviderInterface).
 */
final class ExtraPropertyCatalogPresenter
{
    public function __construct(
        private readonly FormCatalogInterface $formCatalog,
        private readonly GridCatalogInterface $gridCatalog,
        private readonly ApiEndpointCatalogInterface $apiEndpointCatalog,
        private readonly ExtraPropertyConstraintCatalog $constraintCatalog,
        private readonly ExtraPropertyFormTypeMap $formTypeMap,
    ) {
    }

    /**
     * Payloads for the "Advanced form integration" card.
     *
     * @return array{forms: list<FormCatalogEntry>, grids: list<GridCatalogEntry>, apis: list<ApiEndpointEntry>, defaultFormTypes: array<string, class-string>}
     */
    public function presentAdvancedCard(): array
    {
        return [
            'forms' => $this->formCatalog->getAll(),
            'grids' => $this->gridCatalog->getAll(),
            'apis' => $this->apiEndpointCatalog->getAll(),
            'defaultFormTypes' => $this->formTypeMap->getMap(),
        ];
    }

    /**
     * Payload for the "Validation" card.
     *
     * @return array<string, array{defaultOption: ?string, composite: bool, required: list<string>, options: array<string, array{type: string}>}>
     */
    public function presentValidationCard(): array
    {
        return $this->constraintCatalog->getCatalog();
    }
}
