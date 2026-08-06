<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Query;

use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\ValueObject\ExtraPropertyDefinitionId;

/**
 * Retrieves all data for an extra property definition for use in the BO edit form.
 *
 * Returns an EditableExtraPropertyDefinition DTO including structural fields
 * (entity_name, property_name, type, scope, size, sql_index) which are shown
 * as read-only in the edit form, plus all editable metadata.
 */
class GetExtraPropertyDefinitionForEditing
{
    /**
     * @var ExtraPropertyDefinitionId
     */
    protected ExtraPropertyDefinitionId $id;

    /**
     * @param int $id
     */
    public function __construct(int $id)
    {
        $this->id = new ExtraPropertyDefinitionId($id);
    }

    /**
     * @return ExtraPropertyDefinitionId
     */
    public function getId(): ExtraPropertyDefinitionId
    {
        return $this->id;
    }
}
