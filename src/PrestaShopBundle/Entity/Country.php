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


namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Country
 *
 * @ORM\Table(indexes={@ORM\Index(name="country_iso_code", columns={"iso_code"}), @ORM\Index(name="country_", columns={"id_zone"})})
 * @ORM\Entity
 */
class Country
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_zone", type="integer", nullable=false)
     */
    private $idZone;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_currency", type="integer", nullable=false, options={"default":0})
     */
    private $idCurrency = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="iso_code", type="string", length=3, nullable=false)
     */
    private $isoCode;

    /**
     * @var integer
     *
     * @ORM\Column(name="call_prefix", type="integer", nullable=false, options={"default":0})
     */
    private $callPrefix = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean", nullable=false, options={"default":0})
     */
    private $active = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="contains_states", type="boolean", nullable=false, options={"default":0})
     */
    private $containsStates = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="need_identification_number", type="boolean", nullable=false, options={"default":0})
     */
    private $needIdentificationNumber = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="need_zip_code", type="boolean", nullable=false, options={"default":1})
     */
    private $needZipCode = '1';

    /**
     * @var string
     *
     * @ORM\Column(name="zip_code_format", type="string", length=12, nullable=false)
     */
    private $zipCodeFormat = '';

    /**
     * @var boolean
     *
     * @ORM\Column(name="display_tax_label", type="boolean", nullable=false)
     */
    private $displayTaxLabel;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_country", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idCountry;



    /**
     * Set idZone
     *
     * @param integer $idZone
     *
     * @return Country
     */
    public function setIdZone($idZone)
    {
        $this->idZone = $idZone;

        return $this;
    }

    /**
     * Get idZone
     *
     * @return integer
     */
    public function getIdZone()
    {
        return $this->idZone;
    }

    /**
     * Set idCurrency
     *
     * @param integer $idCurrency
     *
     * @return Country
     */
    public function setIdCurrency($idCurrency)
    {
        $this->idCurrency = $idCurrency;

        return $this;
    }

    /**
     * Get idCurrency
     *
     * @return integer
     */
    public function getIdCurrency()
    {
        return $this->idCurrency;
    }

    /**
     * Set isoCode
     *
     * @param string $isoCode
     *
     * @return Country
     */
    public function setIsoCode($isoCode)
    {
        $this->isoCode = $isoCode;

        return $this;
    }

    /**
     * Get isoCode
     *
     * @return string
     */
    public function getIsoCode()
    {
        return $this->isoCode;
    }

    /**
     * Set callPrefix
     *
     * @param integer $callPrefix
     *
     * @return Country
     */
    public function setCallPrefix($callPrefix)
    {
        $this->callPrefix = $callPrefix;

        return $this;
    }

    /**
     * Get callPrefix
     *
     * @return integer
     */
    public function getCallPrefix()
    {
        return $this->callPrefix;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return Country
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set containsStates
     *
     * @param boolean $containsStates
     *
     * @return Country
     */
    public function setContainsStates($containsStates)
    {
        $this->containsStates = $containsStates;

        return $this;
    }

    /**
     * Get containsStates
     *
     * @return boolean
     */
    public function getContainsStates()
    {
        return $this->containsStates;
    }

    /**
     * Set needIdentificationNumber
     *
     * @param boolean $needIdentificationNumber
     *
     * @return Country
     */
    public function setNeedIdentificationNumber($needIdentificationNumber)
    {
        $this->needIdentificationNumber = $needIdentificationNumber;

        return $this;
    }

    /**
     * Get needIdentificationNumber
     *
     * @return boolean
     */
    public function getNeedIdentificationNumber()
    {
        return $this->needIdentificationNumber;
    }

    /**
     * Set needZipCode
     *
     * @param boolean $needZipCode
     *
     * @return Country
     */
    public function setNeedZipCode($needZipCode)
    {
        $this->needZipCode = $needZipCode;

        return $this;
    }

    /**
     * Get needZipCode
     *
     * @return boolean
     */
    public function getNeedZipCode()
    {
        return $this->needZipCode;
    }

    /**
     * Set zipCodeFormat
     *
     * @param string $zipCodeFormat
     *
     * @return Country
     */
    public function setZipCodeFormat($zipCodeFormat)
    {
        $this->zipCodeFormat = $zipCodeFormat;

        return $this;
    }

    /**
     * Get zipCodeFormat
     *
     * @return string
     */
    public function getZipCodeFormat()
    {
        return $this->zipCodeFormat;
    }

    /**
     * Set displayTaxLabel
     *
     * @param boolean $displayTaxLabel
     *
     * @return Country
     */
    public function setDisplayTaxLabel($displayTaxLabel)
    {
        $this->displayTaxLabel = $displayTaxLabel;

        return $this;
    }

    /**
     * Get displayTaxLabel
     *
     * @return boolean
     */
    public function getDisplayTaxLabel()
    {
        return $this->displayTaxLabel;
    }

    /**
     * Get idCountry
     *
     * @return integer
     */
    public function getIdCountry()
    {
        return $this->idCountry;
    }
}
