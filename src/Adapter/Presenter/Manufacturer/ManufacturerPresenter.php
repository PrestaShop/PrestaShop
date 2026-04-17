<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Presenter\Manufacturer;

use Hook;
use Language;
use Link;
use Manufacturer;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;

class ManufacturerPresenter
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
     * @param array|Manufacturer $manufacturer Manufacturer object or an array
     * @param Language $language
     *
     * @return ManufacturerLazyArray
     */
    public function present(array|Manufacturer $manufacturer, Language $language)
    {
        // Convert to array if a Manufacturer object was passed
        if (is_object($manufacturer)) {
            $manufacturer = (array) $manufacturer;
        }

        // Normalize IDs
        if (empty($manufacturer['id_manufacturer'])) {
            $manufacturer['id_manufacturer'] = $manufacturer['id'];
        }
        if (empty($manufacturer['id'])) {
            $manufacturer['id'] = $manufacturer['id_manufacturer'];
        }

        $manufacturerLazyArray = new ManufacturerLazyArray(
            $manufacturer,
            $language,
            $this->imageRetriever,
            $this->link
        );

        Hook::exec('actionPresentManufacturer',
            ['presentedManufacturer' => &$manufacturerLazyArray]
        );

        return $manufacturerLazyArray;
    }
}
