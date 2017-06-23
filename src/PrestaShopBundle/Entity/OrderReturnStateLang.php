<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrderReturnStateLang
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class OrderReturnStateLang
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=64, nullable=false)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_order_return_state", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idOrderReturnState;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_lang", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idLang;



    /**
     * Set name
     *
     * @param string $name
     *
     * @return OrderReturnStateLang
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
     * Set idOrderReturnState
     *
     * @param integer $idOrderReturnState
     *
     * @return OrderReturnStateLang
     */
    public function setIdOrderReturnState($idOrderReturnState)
    {
        $this->idOrderReturnState = $idOrderReturnState;

        return $this;
    }

    /**
     * Get idOrderReturnState
     *
     * @return integer
     */
    public function getIdOrderReturnState()
    {
        return $this->idOrderReturnState;
    }

    /**
     * Set idLang
     *
     * @param integer $idLang
     *
     * @return OrderReturnStateLang
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
