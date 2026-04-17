<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Presenter\Manufacturer;

use Language;
use Link;
use Manufacturer;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Presenter\AbstractLazyArray;
use PrestaShop\PrestaShop\Adapter\Presenter\LazyArrayAttribute;

class ManufacturerLazyArray extends AbstractLazyArray
{
    /**
     * @var ImageRetriever
     */
    private $imageRetriever;

    /**
     * @var Link
     */
    private $link;

    /**
     * @var array
     */
    protected $manufacturer;

    /**
     * @var Language
     */
    private $language;

    public function __construct(
        array $manufacturer,
        Language $language,
        ImageRetriever $imageRetriever,
        Link $link
    ) {
        $this->manufacturer = $manufacturer;
        $this->language = $language;
        $this->imageRetriever = $imageRetriever;
        $this->link = $link;

        parent::__construct();
        $this->appendArray($this->manufacturer);
    }

    /**
     * @return string
     */
    #[LazyArrayAttribute(arrayAccess: true)]
    public function getText()
    {
        return $this->manufacturer['short_description'];
    }

    /**
     * @return string
     */
    #[LazyArrayAttribute(arrayAccess: true)]
    public function getUrl()
    {
        return $this->link->getManufacturerLink($this->manufacturer['id']);
    }

    /**
     * @return array|null
     */
    #[LazyArrayAttribute(arrayAccess: true)]
    public function getImage()
    {
        return $this->imageRetriever->getImage(
            new Manufacturer($this->manufacturer['id'], $this->language->getId()),
            $this->manufacturer['id']
        );
    }

    /**
     * @return int
     */
    #[LazyArrayAttribute(arrayAccess: true)]
    public function getNbProducts()
    {
        if (!isset($this->manufacturer['nb_products'])) {
            $this->manufacturer['nb_products'] = count(
                (new Manufacturer($this->manufacturer['id'], $this->language->getId()))
                    ->getProductsLite($this->language->getId())
            );
        }

        return $this->manufacturer['nb_products'];
    }
}
