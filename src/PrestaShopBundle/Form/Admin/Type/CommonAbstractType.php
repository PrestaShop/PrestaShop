<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Type;

use PrestaShopBundle\Form\FormHelper;
use Symfony\Component\Form\AbstractType;

/**
 * This subclass contains common functions for specific Form types needs.
 *
 * @deprecated since 9.0 use \Symfony\Component\Form\AbstractType instead
 */
abstract class CommonAbstractType extends AbstractType
{
    /**
     * @deprecated since 9.0
     */
    public const PRESTASHOP_DECIMALS = FormHelper::DEFAULT_PRICE_PRECISION;

    /**
     * @deprecated since 9.0
     */
    public const PRESTASHOP_WEIGHT_DECIMALS = FormHelper::DEFAULT_WEIGHT_PRECISION;

    /**
     * Format legacy data list to mapping SF2 form field choice.
     *
     * @param array $list
     * @param string $mapping_value
     * @param string $mapping_name
     *
     * @return array
     */
    protected function formatDataChoicesList($list, $mapping_value = 'id', $mapping_name = 'name')
    {
        @trigger_error(
            sprintf(
                '%s is deprecated since version 9.0 and will be removed in the next major version. Use %s::%s instead.',
                __METHOD__,
                FormHelper::class,
                'formatDataChoicesList()'
            ),
            E_USER_DEPRECATED
        );

        return FormHelper::formatDataChoicesList($list, $mapping_value, $mapping_name);
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
    protected function formatDataDuplicateChoicesList($list, $mapping_value = 'id', $mapping_name = 'name')
    {
        @trigger_error(
            sprintf(
                '%s is deprecated since version 9.0 and will be removed in the next major version. There is no replacement for this method.',
                __METHOD__
            ),
            E_USER_DEPRECATED
        );

        $new_list = [];
        foreach ($list as $item) {
            $new_list[$item[$mapping_value] . ' - ' . $item[$mapping_name]] = $item[$mapping_value];
        }

        return $new_list;
    }
}
