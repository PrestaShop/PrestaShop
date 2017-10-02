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

namespace PrestaShop\PrestaShop\Adapter\Currency;

/**
 * This class is used to provide database data by using legacy code
 * It SHOULD be replaced with new ORM code (not yet implemented)
 * It MUST NOT use too much business code, in order to be easily replaced with ORM
 *
 * @deprecated should be replaced by ORM in new code
 */
class Currency
{
    protected $id = 0;
    protected $name;
    protected $iso_code;
    protected $iso_code_num;
    protected $conversion_rate;
    protected $deleted = 0;
    protected $active;
    protected $sign;
    protected $format;
    protected $blank;
    protected $decimals;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     *
     * @return Currency
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsoCode()
    {
        return $this->iso_code;
    }

    /**
     * @param mixed $iso_code
     *
     * @return Currency
     */
    public function setIsoCode($iso_code)
    {
        $this->iso_code = $iso_code;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsoCodeNum()
    {
        return $this->iso_code_num;
    }

    /**
     * @param mixed $iso_code_num
     *
     * @return Currency
     */
    public function setIsoCodeNum($iso_code_num)
    {
        $this->iso_code_num = $iso_code_num;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getConversionRate()
    {
        return $this->conversion_rate;
    }

    /**
     * @param mixed $conversion_rate
     *
     * @return Currency
     */
    public function setConversionRate($conversion_rate)
    {
        $this->conversion_rate = $conversion_rate;

        return $this;
    }

    /**
     * @return int
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param int $deleted
     *
     * @return Currency
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param mixed $active
     *
     * @return Currency
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSign()
    {
        return $this->sign;
    }

    /**
     * @param mixed $sign
     *
     * @return Currency
     */
    public function setSign($sign)
    {
        $this->sign = $sign;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @param mixed $format
     *
     * @return Currency
     */
    public function setFormat($format)
    {
        $this->format = $format;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getBlank()
    {
        return $this->blank;
    }

    /**
     * @param mixed $blank
     *
     * @return Currency
     */
    public function setBlank($blank)
    {
        $this->blank = $blank;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDecimals()
    {
        return $this->decimals;
    }

    /**
     * @param mixed $decimals
     *
     * @return Currency
     */
    public function setDecimals($decimals)
    {
        $this->decimals = $decimals;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     *
     * @return Currency
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
