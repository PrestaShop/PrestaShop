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
 * MetaLang
 *
 * @ORM\Table(indexes={@ORM\Index(name="id_shop", columns={"id_shop"}), @ORM\Index(name="id_lang", columns={"id_lang"})})
 * @ORM\Entity
 */
class MetaLang
{
    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=128, nullable=true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="keywords", type="string", length=255, nullable=true)
     */
    private $keywords;

    /**
     * @var string
     *
     * @ORM\Column(name="url_rewrite", type="string", length=254, nullable=false)
     */
    private $urlRewrite;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_meta", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idMeta;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_shop", type="integer", options={"default":1})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idShop;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_lang", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idLang;



    /**
     * Set title
     *
     * @param string $title
     *
     * @return MetaLang
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return MetaLang
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set keywords
     *
     * @param string $keywords
     *
     * @return MetaLang
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;

        return $this;
    }

    /**
     * Get keywords
     *
     * @return string
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * Set urlRewrite
     *
     * @param string $urlRewrite
     *
     * @return MetaLang
     */
    public function setUrlRewrite($urlRewrite)
    {
        $this->urlRewrite = $urlRewrite;

        return $this;
    }

    /**
     * Get urlRewrite
     *
     * @return string
     */
    public function getUrlRewrite()
    {
        return $this->urlRewrite;
    }

    /**
     * Set idMeta
     *
     * @param integer $idMeta
     *
     * @return MetaLang
     */
    public function setIdMeta($idMeta)
    {
        $this->idMeta = $idMeta;

        return $this;
    }

    /**
     * Get idMeta
     *
     * @return integer
     */
    public function getIdMeta()
    {
        return $this->idMeta;
    }

    /**
     * Set idShop
     *
     * @param integer $idShop
     *
     * @return MetaLang
     */
    public function setIdShop($idShop)
    {
        $this->idShop = $idShop;

        return $this;
    }

    /**
     * Get idShop
     *
     * @return integer
     */
    public function getIdShop()
    {
        return $this->idShop;
    }

    /**
     * Set idLang
     *
     * @param integer $idLang
     *
     * @return MetaLang
     */
    public function setIdLang($idLang)
    {
        $this->idLang = $idLang;

        return $this;
    }

    /**
     * Get idLang
     *
     * @return integer
     */
    public function getIdLang()
    {
        return $this->idLang;
    }
}
