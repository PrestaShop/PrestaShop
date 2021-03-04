<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use PrestaShopBundle\Translation\Constraints\PassVsprintf;

/**
 * Translation.
 *
 * @ORM\Table(
 *     indexes={@ORM\Index(name="key", columns={"domain"})},
 * )
 * @ORM\Entity(repositoryClass="PrestaShopBundle\Entity\Repository\TranslationRepository")
 * @PassVsprintf
 */
class Translation
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="id_translation", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Lang
     *
     * @ORM\ManyToOne(targetEntity="Lang", inversedBy="translations")
     * @ORM\JoinColumn(name="id_lang", referencedColumnName="id_lang", nullable=false)
     */
    private $lang;

    /**
     * @var string
     *
     * @ORM\Column(name="`key`", type="text", length=8000, options={"collation":"utf8_bin"})
     */
    private $key;

    /**
     * @var string
     *
     * @ORM\Column(name="translation", type="text", length=65500)
     */
    private $translation;

    /**
     * @var string
     *
     * @ORM\Column(name="domain", type="string", length=80)
     */
    private $domain;

    /**
     * @var string
     *
     * @ORM\Column(name="theme", type="string", length=32, nullable=true)
     */
    private $theme = null;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getTranslation()
    {
        return $this->translation;
    }

    /**
     * @return Lang
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param string $key
     *
     * @return \PrestaShopBundle\Entity\Translation
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * @param string $translation
     *
     * @return \PrestaShopBundle\Entity\Translation
     */
    public function setTranslation($translation)
    {
        $this->translation = $translation;

        return $this;
    }

    /**
     * @param Lang $lang
     *
     * @return \PrestaShopBundle\Entity\Translation
     */
    public function setLang(Lang $lang)
    {
        $this->lang = $lang;

        return $this;
    }

    /**
     * @param string $domain
     *
     * @return \PrestaShopBundle\Entity\Translation
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * @return string
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * @param string $theme
     *
     * @return \PrestaShopBundle\Entity\Translation
     */
    public function setTheme($theme)
    {
        $this->theme = $theme;

        return $this;
    }
}
