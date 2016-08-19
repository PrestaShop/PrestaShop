<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Translation
 *
 * @ORM\Table(indexes={@ORM\Index(name="key", columns={"domain"})})
 * @ORM\Entity(repositoryClass="TranslationRepository")
 */
class Translation
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_translation", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="`key`", type="text")
     */
    private $key;

    /**
     * @var string
     *
     * @ORM\Column(name="translation", type="text")
     */
    private $translation;

    /**
     * @var Lang
     * 
     * @ORM\ManyToOne(targetEntity="Lang")
     * @ORM\JoinColumn(name="id_lang", referencedColumnName="id_lang", nullable=false)
     */
    private $lang;

    /**
     * @var string
     *
     * @ORM\Column(name="domain", type="string")
     */
    private $domain;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * 
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }
    
    /**
     * 
     * @return string
     */
    public function getTranslation()
    {
        return $this->translation;
    }

    /**
     * 
     * @return Lang
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * 
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * 
     * @param string $key
     * @return \PrestaShopBundle\Entity\Translation
     */
    public function setKey($key)
    {
        $this->key = $key;
        
        return $this;
    }

    /**
     * 
     * @param string $translation
     * @return \PrestaShopBundle\Entity\Translation
     */
    public function setTranslation($translation)
    {
        $this->translation = $translation;
        
        return $this;
    }

    /**
     * 
     * @param Lang $lang
     * @return \PrestaShopBundle\Entity\Translation
     */
    public function setLang(Lang $lang)
    {
        $this->lang = $lang;
        
        return $this;
    }

    /**
     * 
     * @param string $domain
     * @return \PrestaShopBundle\Entity\Translation
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
        
        return $this;
    }
}
