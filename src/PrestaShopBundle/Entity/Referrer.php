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
 * Referrer
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Referrer
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=64, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="passwd", type="string", length=32, nullable=true)
     */
    private $passwd;

    /**
     * @var string
     *
     * @ORM\Column(name="http_referer_regexp", type="string", length=64, nullable=true)
     */
    private $httpRefererRegexp;

    /**
     * @var string
     *
     * @ORM\Column(name="http_referer_like", type="string", length=64, nullable=true)
     */
    private $httpRefererLike;

    /**
     * @var string
     *
     * @ORM\Column(name="request_uri_regexp", type="string", length=64, nullable=true)
     */
    private $requestUriRegexp;

    /**
     * @var string
     *
     * @ORM\Column(name="request_uri_like", type="string", length=64, nullable=true)
     */
    private $requestUriLike;

    /**
     * @var string
     *
     * @ORM\Column(name="http_referer_regexp_not", type="string", length=64, nullable=true)
     */
    private $httpRefererRegexpNot;

    /**
     * @var string
     *
     * @ORM\Column(name="http_referer_like_not", type="string", length=64, nullable=true)
     */
    private $httpRefererLikeNot;

    /**
     * @var string
     *
     * @ORM\Column(name="request_uri_regexp_not", type="string", length=64, nullable=true)
     */
    private $requestUriRegexpNot;

    /**
     * @var string
     *
     * @ORM\Column(name="request_uri_like_not", type="string", length=64, nullable=true)
     */
    private $requestUriLikeNot;

    /**
     * @var string
     *
     * @ORM\Column(name="base_fee", type="decimal", precision=5, scale=2, nullable=false, options={"default":0.00})
     */
    private $baseFee = '0.00';

    /**
     * @var string
     *
     * @ORM\Column(name="percent_fee", type="decimal", precision=5, scale=2, nullable=false, options={"default":0.00})
     */
    private $percentFee = '0.00';

    /**
     * @var string
     *
     * @ORM\Column(name="click_fee", type="decimal", precision=5, scale=2, nullable=false, options={"default":0.00})
     */
    private $clickFee = '0.00';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_add", type="datetime", nullable=false)
     */
    private $dateAdd;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_referrer", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idReferrer;



    /**
     * Set name
     *
     * @param string $name
     *
     * @return Referrer
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set passwd
     *
     * @param string $passwd
     *
     * @return Referrer
     */
    public function setPasswd($passwd)
    {
        $this->passwd = $passwd;

        return $this;
    }

    /**
     * Get passwd
     *
     * @return string
     */
    public function getPasswd()
    {
        return $this->passwd;
    }

    /**
     * Set httpRefererRegexp
     *
     * @param string $httpRefererRegexp
     *
     * @return Referrer
     */
    public function setHttpRefererRegexp($httpRefererRegexp)
    {
        $this->httpRefererRegexp = $httpRefererRegexp;

        return $this;
    }

    /**
     * Get httpRefererRegexp
     *
     * @return string
     */
    public function getHttpRefererRegexp()
    {
        return $this->httpRefererRegexp;
    }

    /**
     * Set httpRefererLike
     *
     * @param string $httpRefererLike
     *
     * @return Referrer
     */
    public function setHttpRefererLike($httpRefererLike)
    {
        $this->httpRefererLike = $httpRefererLike;

        return $this;
    }

    /**
     * Get httpRefererLike
     *
     * @return string
     */
    public function getHttpRefererLike()
    {
        return $this->httpRefererLike;
    }

    /**
     * Set requestUriRegexp
     *
     * @param string $requestUriRegexp
     *
     * @return Referrer
     */
    public function setRequestUriRegexp($requestUriRegexp)
    {
        $this->requestUriRegexp = $requestUriRegexp;

        return $this;
    }

    /**
     * Get requestUriRegexp
     *
     * @return string
     */
    public function getRequestUriRegexp()
    {
        return $this->requestUriRegexp;
    }

    /**
     * Set requestUriLike
     *
     * @param string $requestUriLike
     *
     * @return Referrer
     */
    public function setRequestUriLike($requestUriLike)
    {
        $this->requestUriLike = $requestUriLike;

        return $this;
    }

    /**
     * Get requestUriLike
     *
     * @return string
     */
    public function getRequestUriLike()
    {
        return $this->requestUriLike;
    }

    /**
     * Set httpRefererRegexpNot
     *
     * @param string $httpRefererRegexpNot
     *
     * @return Referrer
     */
    public function setHttpRefererRegexpNot($httpRefererRegexpNot)
    {
        $this->httpRefererRegexpNot = $httpRefererRegexpNot;

        return $this;
    }

    /**
     * Get httpRefererRegexpNot
     *
     * @return string
     */
    public function getHttpRefererRegexpNot()
    {
        return $this->httpRefererRegexpNot;
    }

    /**
     * Set httpRefererLikeNot
     *
     * @param string $httpRefererLikeNot
     *
     * @return Referrer
     */
    public function setHttpRefererLikeNot($httpRefererLikeNot)
    {
        $this->httpRefererLikeNot = $httpRefererLikeNot;

        return $this;
    }

    /**
     * Get httpRefererLikeNot
     *
     * @return string
     */
    public function getHttpRefererLikeNot()
    {
        return $this->httpRefererLikeNot;
    }

    /**
     * Set requestUriRegexpNot
     *
     * @param string $requestUriRegexpNot
     *
     * @return Referrer
     */
    public function setRequestUriRegexpNot($requestUriRegexpNot)
    {
        $this->requestUriRegexpNot = $requestUriRegexpNot;

        return $this;
    }

    /**
     * Get requestUriRegexpNot
     *
     * @return string
     */
    public function getRequestUriRegexpNot()
    {
        return $this->requestUriRegexpNot;
    }

    /**
     * Set requestUriLikeNot
     *
     * @param string $requestUriLikeNot
     *
     * @return Referrer
     */
    public function setRequestUriLikeNot($requestUriLikeNot)
    {
        $this->requestUriLikeNot = $requestUriLikeNot;

        return $this;
    }

    /**
     * Get requestUriLikeNot
     *
     * @return string
     */
    public function getRequestUriLikeNot()
    {
        return $this->requestUriLikeNot;
    }

    /**
     * Set baseFee
     *
     * @param string $baseFee
     *
     * @return Referrer
     */
    public function setBaseFee($baseFee)
    {
        $this->baseFee = $baseFee;

        return $this;
    }

    /**
     * Get baseFee
     *
     * @return string
     */
    public function getBaseFee()
    {
        return $this->baseFee;
    }

    /**
     * Set percentFee
     *
     * @param string $percentFee
     *
     * @return Referrer
     */
    public function setPercentFee($percentFee)
    {
        $this->percentFee = $percentFee;

        return $this;
    }

    /**
     * Get percentFee
     *
     * @return string
     */
    public function getPercentFee()
    {
        return $this->percentFee;
    }

    /**
     * Set clickFee
     *
     * @param string $clickFee
     *
     * @return Referrer
     */
    public function setClickFee($clickFee)
    {
        $this->clickFee = $clickFee;

        return $this;
    }

    /**
     * Get clickFee
     *
     * @return string
     */
    public function getClickFee()
    {
        return $this->clickFee;
    }

    /**
     * Set dateAdd
     *
     * @param \DateTime $dateAdd
     *
     * @return Referrer
     */
    public function setDateAdd($dateAdd)
    {
        $this->dateAdd = $dateAdd;

        return $this;
    }

    /**
     * Get dateAdd
     *
     * @return \DateTime
     */
    public function getDateAdd()
    {
        return $this->dateAdd;
    }

    /**
     * Get idReferrer
     *
     * @return integer
     */
    public function getIdReferrer()
    {
        return $this->idReferrer;
    }
}
