<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder;

use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;

class CommandField
{
    /**
     * @var string
     */
    private $commandSetter;

    /**
     * @var array<int, DataField>
     */
    private $dataFields;

    /**
     * @var bool
     */
    private $isMultiShopField;

    protected function __construct(string $commandSetter, array $dataFields, bool $isMultiShopField)
    {
        if (empty($dataFields)) {
            throw new InvalidArgumentException(
                sprintf('No data field provided to command setter "%s"', $commandSetter)
            );
        }
        foreach ($dataFields as $dataField) {
            if (!$dataField instanceof DataField) {
                throw new InvalidArgumentException(sprintf(
                    'Invalid data field type "%s", expected "%s"',
                    get_debug_type($dataField),
                    DataField::class
                ));
            }
        }
        $this->commandSetter = $commandSetter;
        $this->dataFields = array_values($dataFields);
        $this->isMultiShopField = $isMultiShopField;
    }

    /**
     * Returns a new command field for single shop
     *
     * @param string $commandSetter
     * @param array<int, DataField> $dataFields
     *
     * @return static
     */
    public static function createAsSingleShop(string $commandSetter, array $dataFields): self
    {
        return new static($commandSetter, $dataFields, false);
    }

    /**
     * Returns a new command field for multiple shops
     *
     * @param string $commandSetter
     * @param array<int, DataField> $dataFields
     *
     * @return static
     */
    public static function createAsMultiShop(string $commandSetter, array $dataFields): self
    {
        return new static($commandSetter, $dataFields, true);
    }

    /**
     * @return string
     */
    public function getCommandSetter(): string
    {
        return $this->commandSetter;
    }

    /**
     * @return array<int, DataField>
     */
    public function getDataFields(): array
    {
        return $this->dataFields;
    }

    /**
     * @return bool
     */
    public function isMultiShopField(): bool
    {
        return $this->isMultiShopField;
    }
}
