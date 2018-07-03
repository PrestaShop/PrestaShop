<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Grid\Column;

/**
 * Define a Column filter option.
 */
final class ColumnFilterOption
{
    /**
     * @var string class name that must implements FormTypeInterface
     */
    private $filterType;

    /**
     * @var array
     */
    private $filterTypeOptions;

    /**
     * @param string $filterType
     * @param array $filterTypeOptions
     */
    public function __construct($filterType, array $filterTypeOptions = [])
    {
        $this->filterType = $filterType;
        $this->filterTypeOptions = $filterTypeOptions;
    }

    /**
     * @return string the class name of Form Type.
     */
    public function getFilterType()
    {
        return $this->filterType;
    }

    /**
     * @return array the form type options if any.
     */
    public function getFilterTypeOptions()
    {
        return $this->filterTypeOptions;
    }
}
