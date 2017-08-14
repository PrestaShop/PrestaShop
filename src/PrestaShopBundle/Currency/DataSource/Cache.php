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

namespace PrestaShopBundle\Currency\DataSource;

use InvalidArgumentException;

class Cache implements DataSourceInterface
{
    protected $stubData = array(
        'EUR' => 978,
        'USD' => 840,
        'GBP' => 826,
    );

    /**
     * The contextual locale code.. Data will be returned in this language.
     *
     * @var string
     */
    protected $localeCode;

    /**
     * Cache constructor.
     *
     * @param $localeCode
     */
    public function __construct($localeCode)
    {
        $this->localeCode = (string)$localeCode;
    }

    public function getLocaleCode()
    {
        return $this->localeCode;
    }

    public function setLocaleCode($localeCode)
    {
        $this->localeCode = (string)$localeCode;

        return $this;
    }

    /**
     * Get currency data by internal database identifier
     *
     * @param int $id
     *
     * @return array The currency data
     */
    public function getCurrencyById($id)
    {
        if (!is_int($id)) {
            throw new InvalidArgumentException('$id must be an integer');
        }

        $id = (int)$id;

        if (empty($this->stubData[$id])) {
            return [];
        }

        return [
            'id'             => $id,
            'numericIsoCode' => $this->stubData[$id],
        ];
    }

    /**
     * Get currency data by ISO 4217 code
     *
     * @param string $isoCode
     *
     * @return array The currency data
     */
    public function getCurrencyByIsoCode($isoCode)
    {
        if (empty($this->stubData[$isoCode])) {
            return [];
        }

        return [
            'isoCode'        => $isoCode,
            'numericIsoCode' => $this->stubData[$isoCode],
        ];
    }
}
