<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Presenter\Supplier;

use Hook;
use Language;
use Link;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use Supplier;

class SupplierPresenter
{
    /**
     * @var ImageRetriever
     */
    protected $imageRetriever;

    /**
     * @var Link
     */
    protected $link;

    public function __construct(Link $link)
    {
        $this->link = $link;
        $this->imageRetriever = new ImageRetriever($link);
    }

    /**
     * @param array|Supplier $supplier Supplier object or an array
     * @param Language $language
     *
     * @return SupplierLazyArray
     */
    public function present(array|Supplier $supplier, Language $language)
    {
        // Convert to array if a Supplier object was passed
        if (is_object($supplier)) {
            $supplier = (array) $supplier;
        }

        // Normalize IDs
        if (empty($supplier['id_supplier'])) {
            $supplier['id_supplier'] = $supplier['id'];
        }
        if (empty($supplier['id'])) {
            $supplier['id'] = $supplier['id_supplier'];
        }

        $supplierLazyArray = new SupplierLazyArray(
            $supplier,
            $language,
            $this->imageRetriever,
            $this->link
        );

        Hook::exec('actionPresentSupplier',
            ['presentedSupplier' => &$supplierLazyArray]
        );

        return $supplierLazyArray;
    }
}
