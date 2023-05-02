<?php

namespace PrestaShopBundle\Form;

class FormHelper
{
    public const DEFAULT_PRICE_PRECISION = 6;
    public const DEFAULT_WEIGHT_PRECISION = 6;

    /**
     * Format legacy data list to mapping SF2 form field choice.
     *
     * @param array $list
     * @param string $mapping_value
     * @param string $mapping_name
     *
     * @return array
     */
    public static function formatDataChoicesList($list, $mapping_value = 'id', $mapping_name = 'name')
    {
        $new_list = [];
        foreach ($list as $item) {
            if (array_key_exists($item[$mapping_name], $new_list)) {
                return self::formatDataDuplicateChoicesList($list, $mapping_value, $mapping_name);
            } else {
                $new_list[$item[$mapping_name]] = $item[$mapping_value];
            }
        }

        return $new_list;
    }

    /**
     * Format legacy data list to mapping SF2 form field choice (possibility to have 2 name equals).
     *
     * @param array $list
     * @param string $mapping_value
     * @param string $mapping_name
     *
     * @return array
     */
    private static function formatDataDuplicateChoicesList($list, $mapping_value = 'id', $mapping_name = 'name')
    {
        $new_list = [];
        foreach ($list as $item) {
            $new_list[$item[$mapping_value] . ' - ' . $item[$mapping_name]] = $item[$mapping_value];
        }

        return $new_list;
    }
}
