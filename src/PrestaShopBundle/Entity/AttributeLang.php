<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AttributeLang.
 *
 * @ORM\Table()
 *
 * @ORM\Entity(repositoryClass="PrestaShopBundle\Entity\Repository\AttributeLangRepository")
 */
class AttributeLang
{
    /**
     * @ORM\Id
     *
     * @ORM\ManyToOne(targetEntity="PrestaShopBundle\Entity\Attribute", inversedBy="attributeLangs")
     *
     * @ORM\JoinColumn(name="id_attribute", referencedColumnName="id_attribute", nullable=false)
     */
    private Attribute $attribute;

    /**
     * @ORM\Id
     *
     * @ORM\ManyToOne(targetEntity="PrestaShopBundle\Entity\Lang")
     *
     * @ORM\JoinColumn(name="id_lang", referencedColumnName="id_lang", nullable=false, onDelete="CASCADE")
     */
    private Lang $lang;

    /**
     * @ORM\Column(name="name", type="string", length=128)
     */
    private string $name;

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setAttribute(Attribute $attribute): static
    {
        $this->attribute = $attribute;

        return $this;
    }

    public function getAttribute(): Attribute
    {
        return $this->attribute;
    }

    public function setLang(Lang $lang): static
    {
        $this->lang = $lang;

        return $this;
    }

    public function getLang(): Lang
    {
        return $this->lang;
    }
}
