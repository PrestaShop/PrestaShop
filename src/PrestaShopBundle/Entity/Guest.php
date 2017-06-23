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
 * Guest
 *
 * @ORM\Table(indexes={@ORM\Index(name="id_customer", columns={"id_customer"}), @ORM\Index(name="id_operating_system", columns={"id_operating_system"}), @ORM\Index(name="id_web_browser", columns={"id_web_browser"})})
 * @ORM\Entity
 */
class Guest
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_operating_system", type="integer", nullable=true)
     */
    private $idOperatingSystem;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_web_browser", type="integer", nullable=true)
     */
    private $idWebBrowser;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_customer", type="integer", nullable=true)
     */
    private $idCustomer;

    /**
     * @var boolean
     *
     * @ORM\Column(name="javascript", type="boolean", nullable=true)
     */
    private $javascript = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="screen_resolution_x", type="smallint", nullable=true)
     */
    private $screenResolutionX;

    /**
     * @var integer
     *
     * @ORM\Column(name="screen_resolution_y", type="smallint", nullable=true)
     */
    private $screenResolutionY;

    /**
     * @var boolean
     *
     * @ORM\Column(name="screen_color", type="boolean", nullable=true)
     */
    private $screenColor;

    /**
     * @var boolean
     *
     * @ORM\Column(name="sun_java", type="boolean", nullable=true)
     */
    private $sunJava;

    /**
     * @var boolean
     *
     * @ORM\Column(name="adobe_flash", type="boolean", nullable=true)
     */
    private $adobeFlash;

    /**
     * @var boolean
     *
     * @ORM\Column(name="adobe_director", type="boolean", nullable=true)
     */
    private $adobeDirector;

    /**
     * @var boolean
     *
     * @ORM\Column(name="apple_quicktime", type="boolean", nullable=true)
     */
    private $appleQuicktime;

    /**
     * @var boolean
     *
     * @ORM\Column(name="real_player", type="boolean", nullable=true)
     */
    private $realPlayer;

    /**
     * @var boolean
     *
     * @ORM\Column(name="windows_media", type="boolean", nullable=true)
     */
    private $windowsMedia;

    /**
     * @var string
     *
     * @ORM\Column(name="accept_language", type="string", length=8, nullable=true)
     */
    private $acceptLanguage;

    /**
     * @var boolean
     *
     * @ORM\Column(name="mobile_theme", type="boolean", nullable=false)
     */
    private $mobileTheme = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="id_guest", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idGuest;



    /**
     * Set idOperatingSystem
     *
     * @param integer $idOperatingSystem
     *
     * @return Guest
     */
    public function setIdOperatingSystem($idOperatingSystem)
    {
        $this->idOperatingSystem = $idOperatingSystem;

        return $this;
    }

    /**
     * Get idOperatingSystem
     *
     * @return integer
     */
    public function getIdOperatingSystem()
    {
        return $this->idOperatingSystem;
    }

    /**
     * Set idWebBrowser
     *
     * @param integer $idWebBrowser
     *
     * @return Guest
     */
    public function setIdWebBrowser($idWebBrowser)
    {
        $this->idWebBrowser = $idWebBrowser;

        return $this;
    }

    /**
     * Get idWebBrowser
     *
     * @return integer
     */
    public function getIdWebBrowser()
    {
        return $this->idWebBrowser;
    }

    /**
     * Set idCustomer
     *
     * @param integer $idCustomer
     *
     * @return Guest
     */
    public function setIdCustomer($idCustomer)
    {
        $this->idCustomer = $idCustomer;

        return $this;
    }

    /**
     * Get idCustomer
     *
     * @return integer
     */
    public function getIdCustomer()
    {
        return $this->idCustomer;
    }

    /**
     * Set javascript
     *
     * @param boolean $javascript
     *
     * @return Guest
     */
    public function setJavascript($javascript)
    {
        $this->javascript = $javascript;

        return $this;
    }

    /**
     * Get javascript
     *
     * @return boolean
     */
    public function getJavascript()
    {
        return $this->javascript;
    }

    /**
     * Set screenResolutionX
     *
     * @param integer $screenResolutionX
     *
     * @return Guest
     */
    public function setScreenResolutionX($screenResolutionX)
    {
        $this->screenResolutionX = $screenResolutionX;

        return $this;
    }

    /**
     * Get screenResolutionX
     *
     * @return integer
     */
    public function getScreenResolutionX()
    {
        return $this->screenResolutionX;
    }

    /**
     * Set screenResolutionY
     *
     * @param integer $screenResolutionY
     *
     * @return Guest
     */
    public function setScreenResolutionY($screenResolutionY)
    {
        $this->screenResolutionY = $screenResolutionY;

        return $this;
    }

    /**
     * Get screenResolutionY
     *
     * @return integer
     */
    public function getScreenResolutionY()
    {
        return $this->screenResolutionY;
    }

    /**
     * Set screenColor
     *
     * @param boolean $screenColor
     *
     * @return Guest
     */
    public function setScreenColor($screenColor)
    {
        $this->screenColor = $screenColor;

        return $this;
    }

    /**
     * Get screenColor
     *
     * @return boolean
     */
    public function getScreenColor()
    {
        return $this->screenColor;
    }

    /**
     * Set sunJava
     *
     * @param boolean $sunJava
     *
     * @return Guest
     */
    public function setSunJava($sunJava)
    {
        $this->sunJava = $sunJava;

        return $this;
    }

    /**
     * Get sunJava
     *
     * @return boolean
     */
    public function getSunJava()
    {
        return $this->sunJava;
    }

    /**
     * Set adobeFlash
     *
     * @param boolean $adobeFlash
     *
     * @return Guest
     */
    public function setAdobeFlash($adobeFlash)
    {
        $this->adobeFlash = $adobeFlash;

        return $this;
    }

    /**
     * Get adobeFlash
     *
     * @return boolean
     */
    public function getAdobeFlash()
    {
        return $this->adobeFlash;
    }

    /**
     * Set adobeDirector
     *
     * @param boolean $adobeDirector
     *
     * @return Guest
     */
    public function setAdobeDirector($adobeDirector)
    {
        $this->adobeDirector = $adobeDirector;

        return $this;
    }

    /**
     * Get adobeDirector
     *
     * @return boolean
     */
    public function getAdobeDirector()
    {
        return $this->adobeDirector;
    }

    /**
     * Set appleQuicktime
     *
     * @param boolean $appleQuicktime
     *
     * @return Guest
     */
    public function setAppleQuicktime($appleQuicktime)
    {
        $this->appleQuicktime = $appleQuicktime;

        return $this;
    }

    /**
     * Get appleQuicktime
     *
     * @return boolean
     */
    public function getAppleQuicktime()
    {
        return $this->appleQuicktime;
    }

    /**
     * Set realPlayer
     *
     * @param boolean $realPlayer
     *
     * @return Guest
     */
    public function setRealPlayer($realPlayer)
    {
        $this->realPlayer = $realPlayer;

        return $this;
    }

    /**
     * Get realPlayer
     *
     * @return boolean
     */
    public function getRealPlayer()
    {
        return $this->realPlayer;
    }

    /**
     * Set windowsMedia
     *
     * @param boolean $windowsMedia
     *
     * @return Guest
     */
    public function setWindowsMedia($windowsMedia)
    {
        $this->windowsMedia = $windowsMedia;

        return $this;
    }

    /**
     * Get windowsMedia
     *
     * @return boolean
     */
    public function getWindowsMedia()
    {
        return $this->windowsMedia;
    }

    /**
     * Set acceptLanguage
     *
     * @param string $acceptLanguage
     *
     * @return Guest
     */
    public function setAcceptLanguage($acceptLanguage)
    {
        $this->acceptLanguage = $acceptLanguage;

        return $this;
    }

    /**
     * Get acceptLanguage
     *
     * @return string
     */
    public function getAcceptLanguage()
    {
        return $this->acceptLanguage;
    }

    /**
     * Set mobileTheme
     *
     * @param boolean $mobileTheme
     *
     * @return Guest
     */
    public function setMobileTheme($mobileTheme)
    {
        $this->mobileTheme = $mobileTheme;

        return $this;
    }

    /**
     * Get mobileTheme
     *
     * @return boolean
     */
    public function getMobileTheme()
    {
        return $this->mobileTheme;
    }

    /**
     * Get idGuest
     *
     * @return integer
     */
    public function getIdGuest()
    {
        return $this->idGuest;
    }
}
