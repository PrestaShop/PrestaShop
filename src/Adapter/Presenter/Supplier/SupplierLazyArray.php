<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Presenter\Supplier;

use Language;
use Link;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Presenter\AbstractLazyArray;
use PrestaShop\PrestaShop\Adapter\Presenter\LazyArrayAttribute;
use Supplier;

class SupplierLazyArray extends AbstractLazyArray
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
    protected $supplier;

    /**
     * @var Language
     */
    private $language;

    public function __construct(
        array $supplier,
        Language $language,
        ImageRetriever $imageRetriever,
        Link $link
    ) {
        $this->supplier = $supplier;
        $this->language = $language;
        $this->imageRetriever = $imageRetriever;
        $this->link = $link;

        parent::__construct();
        $this->appendArray($this->supplier);
    }

    /**
     * @return string
     */
    #[LazyArrayAttribute(arrayAccess: true)]
    public function getUrl()
    {
        return $this->link->getSupplierLink($this->supplier['id']);
    }

    /**
     * @return array|null
     */
    #[LazyArrayAttribute(arrayAccess: true)]
    public function getImage()
    {
        return $this->imageRetriever->getImage(
            new Supplier($this->supplier['id'], $this->language->getId()),
            $this->supplier['id']
        );
    }

    /**
     * @return int
     */
    #[LazyArrayAttribute(arrayAccess: true)]
    public function getNbProducts()
    {
        if (!isset($this->supplier['nb_products'])) {
            $this->supplier['nb_products'] = count(
                (new Supplier($this->supplier['id'], $this->language->getId()))
                    ->getProductsLite($this->language->getId())
            );
        }

        return $this->supplier['nb_products'];
    }
}
