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
 * SmartyCache
 *
 * @ORM\Table(indexes={@ORM\Index(name="name", columns={"name"}), @ORM\Index(name="cache_id", columns={"cache_id"}), @ORM\Index(name="modified", columns={"modified"})})
 * @ORM\Entity
 */
class SmartyCache
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=40, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="cache_id", type="string", length=254, nullable=true)
     */
    private $cacheId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modified", type="datetime", nullable=false)
     */
    private $modified = 'CURRENT_TIMESTAMP';

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", nullable=false)
     */
    private $content;

    /**
     * @var string
     *
     * @ORM\Column(name="id_smarty_cache", type="string", length=40)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idSmartyCache;



    /**
     * Set name
     *
     * @param string $name
     *
     * @return SmartyCache
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
     * Set cacheId
     *
     * @param string $cacheId
     *
     * @return SmartyCache
     */
    public function setCacheId($cacheId)
    {
        $this->cacheId = $cacheId;

        return $this;
    }

    /**
     * Get cacheId
     *
     * @return string
     */
    public function getCacheId()
    {
        return $this->cacheId;
    }

    /**
     * Set modified
     *
     * @param \DateTime $modified
     *
     * @return SmartyCache
     */
    public function setModified($modified)
    {
        $this->modified = $modified;

        return $this;
    }

    /**
     * Get modified
     *
     * @return \DateTime
     */
    public function getModified()
    {
        return $this->modified;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return SmartyCache
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Get idSmartyCache
     *
     * @return string
     */
    public function getIdSmartyCache()
    {
        return $this->idSmartyCache;
    }
}
