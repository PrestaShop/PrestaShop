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
 * SmartyLazyCache
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class SmartyLazyCache
{
    /**
     * @var string
     *
     * @ORM\Column(name="filepath", type="string", length=255, nullable=false)
     */
    private $filepath = '';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_update", type="datetime", nullable=false)
     */
    private $lastUpdate = '0000-00-00 00:00:00';

    /**
     * @var string
     *
     * @ORM\Column(name="template_hash", type="string", length=32)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $templateHash;

    /**
     * @var string
     *
     * @ORM\Column(name="cache_id", type="string", length=255)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $cacheId;

    /**
     * @var string
     *
     * @ORM\Column(name="compile_id", type="string", length=32)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $compileId;



    /**
     * Set filepath
     *
     * @param string $filepath
     *
     * @return SmartyLazyCache
     */
    public function setFilepath($filepath)
    {
        $this->filepath = $filepath;

        return $this;
    }

    /**
     * Get filepath
     *
     * @return string
     */
    public function getFilepath()
    {
        return $this->filepath;
    }

    /**
     * Set lastUpdate
     *
     * @param \DateTime $lastUpdate
     *
     * @return SmartyLazyCache
     */
    public function setLastUpdate($lastUpdate)
    {
        $this->lastUpdate = $lastUpdate;

        return $this;
    }

    /**
     * Get lastUpdate
     *
     * @return \DateTime
     */
    public function getLastUpdate()
    {
        return $this->lastUpdate;
    }

    /**
     * Set templateHash
     *
     * @param string $templateHash
     *
     * @return SmartyLazyCache
     */
    public function setTemplateHash($templateHash)
    {
        $this->templateHash = $templateHash;

        return $this;
    }

    /**
     * Get templateHash
     *
     * @return string
     */
    public function getTemplateHash()
    {
        return $this->templateHash;
    }

    /**
     * Set cacheId
     *
     * @param string $cacheId
     *
     * @return SmartyLazyCache
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
     * Set compileId
     *
     * @param string $compileId
     *
     * @return SmartyLazyCache
     */
    public function setCompileId($compileId)
    {
        $this->compileId = $compileId;

        return $this;
    }

    /**
     * Get compileId
     *
     * @return string
     */
    public function getCompileId()
    {
        return $this->compileId;
    }
}
