<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TabLang.
 *
 * @ORM\Table()
 *
 * @ORM\Entity(repositoryClass="PrestaShopBundle\Entity\Repository\TabLangRepository")
 */
class TabLang
{
    /**
     * @ORM\Id
     *
     * @ORM\ManyToOne(targetEntity="PrestaShopBundle\Entity\Tab", inversedBy="tabLangs")
     *
     * @ORM\JoinColumn(name="id_tab", referencedColumnName="id_tab", nullable=false)
     */
    private Tab $tab;

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

    public function getTab(): Tab
    {
        return $this->tab;
    }

    public function setTab(Tab $tab): static
    {
        $this->tab = $tab;

        return $this;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
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
