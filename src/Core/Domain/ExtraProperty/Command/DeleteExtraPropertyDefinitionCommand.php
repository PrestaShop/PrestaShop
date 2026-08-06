<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Command;

use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\ValueObject\ExtraPropertyDefinitionId;

/**
 * Deletes an extra property definition row and optionally drops its physical SQL column.
 *
 * Only core definitions (module_name = null) can be deleted via the BO UI.
 * The handler rejects the command for module-owned definitions.
 */
class DeleteExtraPropertyDefinitionCommand
{
    /**
     * @var ExtraPropertyDefinitionId
     */
    protected ExtraPropertyDefinitionId $id;

    /**
     * @param int $id
     * @param bool $dropColumn When true, the physical column in {entity}_extra table is also dropped
     */
    public function __construct(int $id, protected readonly bool $dropColumn = true)
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

    /**
     * @return bool
     */
    public function shouldDropColumn(): bool
    {
        return $this->dropColumn;
    }
}
