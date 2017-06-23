<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ImageLang
 *
 * @ORM\Table(indexes={@ORM\Index(name="id_image", columns={"id_image"})})
 * @ORM\Entity
 */
class ImageLang
{
    /**
     * @var string
     *
     * @ORM\Column(name="legend", type="string", length=128, nullable=true)
     */
    private $legend;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_image", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idImage;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_lang", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idLang;



    /**
     * Set legend
     *
     * @param string $legend
     *
     * @return ImageLang
     */
    public function setLegend($legend)
    {
        $this->legend = $legend;

        return $this;
    }

    /**
     * Get legend
     *
     * @return string
     */
    public function getLegend()
    {
        return $this->legend;
    }

    /**
     * Set idImage
     *
     * @param integer $idImage
     *
     * @return ImageLang
     */
    public function setIdImage($idImage)
    {
        $this->idImage = $idImage;

        return $this;
    }

    /**
     * Get idImage
     *
     * @return integer
     */
    public function getIdImage()
    {
        return $this->idImage;
    }

    /**
     * Set idLang
     *
     * @param integer $idLang
     *
     * @return ImageLang
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
