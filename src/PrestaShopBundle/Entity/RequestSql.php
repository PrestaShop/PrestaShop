<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RequestSql
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class RequestSql
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=200, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="sql", type="text", length=65535, nullable=false)
     */
    private $sql;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_request_sql", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idRequestSql;



    /**
     * Set name
     *
     * @param string $name
     *
     * @return RequestSql
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
     * Set sql
     *
     * @param string $sql
     *
     * @return RequestSql
     */
    public function setSql($sql)
    {
        $this->sql = $sql;

        return $this;
    }

    /**
     * Get sql
     *
     * @return string
     */
    public function getSql()
    {
        return $this->sql;
    }

    /**
     * Get idRequestSql
     *
     * @return integer
     */
    public function getIdRequestSql()
    {
        return $this->idRequestSql;
    }
}
