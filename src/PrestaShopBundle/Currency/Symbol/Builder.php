<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Currency\Symbol;

use PrestaShopBundle\Currency\Exception\Exception;
use PrestaShopBundle\Currency\Symbol;

class Builder
{
    /**
     * @var string Default notation of this symbol
     */
    protected $default;

    /**
     * @var string Narrow notation of this symbol
     */
    protected $narrow;

    public function getDefault()
    {
        return $this->default;
    }

    public function getNarrow()
    {
        return $this->narrow;
    }

    public function setDefault($value)
    {
        $this->default = $value;

        return $this;
    }

    public function setNarrow($value)
    {
        $this->narrow = $value;

        return $this;
    }

    /**
     * @return Symbol
     */
    public function build()
    {
        $this->validateProperties();

        return new Symbol($this);
    }

    protected function validateProperties()
    {
        if (is_null($this->getDefault())) {
            throw new Exception('Default symbol notation must be set');
        }
    }
}
