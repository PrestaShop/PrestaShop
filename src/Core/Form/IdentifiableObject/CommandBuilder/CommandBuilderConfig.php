<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder;

/**
 * Configuration of command fields used by the CommandBuilder component in charge of updating the command object
 * according to input data. Each command field is associated to:
 * - a setter method of the command object
 * - a set of one or mutliple data field descriptions
 * Example:
 *
 * $config = new CommandBuilderConfig('modify_all_');
 * $config
 *     ->addField('[name]', 'setName', DataField::TYPE_STRING)
 *     ->addCompoundField('setIsValid', [
 *         '[command][isValid]' => DataField::TYPE_BOOL,
 *         '[command][foo]' => [
 *             'type' => DataField::TYPE_STRING,
 *             'default' => 'bar',
 *         ],
 *     ])
 *     ->addMultiShopField('[count_number]', 'setCount', DataField::TYPE_INT)
 *     ->addMultiShopCompoundField('setChildren', [
 *         '[parent][children]' => DataField::TYPE_ARRAY,
 *         '[command][foo]' => [
 *             'type' => DataField::TYPE_STRING,
 *             'default' => 'bar',
 *         ],
 *     ])
 * ;
 */
class CommandBuilderConfig
{
    public const FIELD_TYPE_OPTION = 'type';
    public const FIELD_DEFAULT_VALUE_OPTION = 'default';

    /**
     * @var string
     */
    private $modifyAllNamePrefix;

    /**
     * @var array<int, CommandField>
     */
    private $fields = [];

    public function __construct(string $modifyAllNamePrefix = '')
    {
        $this->modifyAllNamePrefix = $modifyAllNamePrefix;
    }

    /**
     * @param string $propertyPath
     * @param string $commandSetter
     * @param string $propertyType
     *
     * @return static
     *
     * @throws DataFieldException
     */
    public function addField(string $propertyPath, string $commandSetter, string $propertyType): self
    {
        $this->fields[] = CommandField::createAsSingleShop(
            $commandSetter,
            $this->createDataFields([
                $propertyPath => $propertyType,
            ])
        );

        return $this;
    }

    /**
     * @param string $propertyPath
     * @param string $commandSetter
     * @param string $propertyType
     *
     * @return static
     *
     * @throws DataFieldException
     */
    public function addMultiShopField(string $propertyPath, string $commandSetter, string $propertyType): self
    {
        $this->fields[] = CommandField::createAsMultiShop(
            $commandSetter,
            $this->createDataFields([
                $propertyPath => $propertyType,
            ])
        );

        return $this;
    }

    /**
     * @param string $commandSetter
     * @param array<string, string|array<string, mixed>> $dataFieldDescriptions
     *
     * @return static
     *
     * @throws DataFieldException
     */
    public function addCompoundField(string $commandSetter, array $dataFieldDescriptions): self
    {
        $this->fields[] = CommandField::createAsSingleShop(
            $commandSetter,
            $this->createDataFields($dataFieldDescriptions)
        );

        return $this;
    }

    /**
     * @param string $commandSetter
     * @param array<string, string|array<string, mixed>> $dataFieldDescriptions
     *
     * @return static
     *
     * @throws DataFieldException
     */
    public function addMultiShopCompoundField(string $commandSetter, array $dataFieldDescriptions): self
    {
        $this->fields[] = CommandField::createAsMultiShop(
            $commandSetter,
            $this->createDataFields($dataFieldDescriptions)
        );

        return $this;
    }

    /**
     * @return string
     */
    public function getModifyAllNamePrefix(): string
    {
        return $this->modifyAllNamePrefix;
    }

    /**
     * @return array<int, CommandField>
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @param array<string, string|array<string, mixed>> $dataFieldDescriptions
     *
     * @return array<int, DataField>
     *
     * @throws DataFieldException
     */
    protected function createDataFields(array $dataFieldDescriptions): array
    {
        $dataFields = [];

        foreach ($dataFieldDescriptions as $path => $dataFieldDescription) {
            $dataFields[] = $this->createDataField($path, $dataFieldDescription);
        }

        return $dataFields;
    }

    /**
     * @param string $path
     * @param string|array<string, mixed> $dataFieldDescription
     *
     * @return DataField
     *
     * @throws DataFieldException
     */
    protected function createDataField(string $path, $dataFieldDescription): DataField
    {
        if (is_string($dataFieldDescription)) {
            $dataFieldDescription = [
                static::FIELD_TYPE_OPTION => $dataFieldDescription,
            ];
        } elseif (!is_array($dataFieldDescription)) {
            throw new DataFieldException(
                sprintf(
                    'Type "%s" is not supported as configuration of data field "%s", expected array or string',
                    $path,
                    gettype($dataFieldDescription)
                )
            );
        }
        $dataFieldArguments = [
            $path,
            $dataFieldDescription[static::FIELD_TYPE_OPTION],
        ];
        if (array_key_exists(self::FIELD_DEFAULT_VALUE_OPTION, $dataFieldDescription)) {
            $dataFieldArguments[] = $dataFieldDescription[static::FIELD_DEFAULT_VALUE_OPTION];
        }

        return new DataField(...$dataFieldArguments);
    }
}
