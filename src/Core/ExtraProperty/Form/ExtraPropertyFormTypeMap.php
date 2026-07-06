<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Form;

use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyType;
use PrestaShopBundle\Form\Admin\Type\DatePickerType;
use PrestaShopBundle\Form\Admin\Type\FormattedTextareaType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormTypeInterface;

/**
 * Maps a logical extra property type to the Symfony form type (and base options) used to render
 * it in back-office forms when the definition does not declare an explicit formType.
 *
 * The definition's formOptions are merged OVER the base options returned here, so a definition
 * can refine the defaults (e.g. change the NumberType scale) without replacing the whole type.
 */
class ExtraPropertyFormTypeMap
{
    /**
     * FLOAT storage columns are DECIMAL(20,6) (see ColumnDefinitionMapper), so the form input
     * accepts the same precision by default.
     */
    private const FLOAT_SCALE = 6;

    /**
     * @param list<string>|null $enumValues ENUM literals of a CHOICE definition (null for other types)
     *
     * @return array{0: class-string<FormTypeInterface>, 1: array<string, mixed>}
     */
    public function getDefaultFor(ExtraPropertyType $type, ?array $enumValues = null): array
    {
        return match ($type) {
            ExtraPropertyType::INT => [IntegerType::class, []],
            ExtraPropertyType::BOOL => [SwitchType::class, []],
            ExtraPropertyType::STRING => [TextType::class, []],
            ExtraPropertyType::FLOAT => [NumberType::class, ['scale' => self::FLOAT_SCALE]],
            ExtraPropertyType::DATE => [DatePickerType::class, []],
            ExtraPropertyType::HTML => [FormattedTextareaType::class, []],
            ExtraPropertyType::JSON => [TextareaType::class, []],
            ExtraPropertyType::CHOICE => [ChoiceType::class, [
                'choices' => array_combine($enumValues ?? [], $enumValues ?? []),
            ]],
        };
    }

    /**
     * Type value => form type FQCN, e.g. for displaying the effective default next to the
     * "Symfony form type" override field of the definition form.
     *
     * @return array<string, class-string<FormTypeInterface>>
     */
    public function getMap(): array
    {
        $map = [];
        foreach (ExtraPropertyType::cases() as $type) {
            $map[$type->value] = $this->getDefaultFor($type)[0];
        }

        return $map;
    }
}
