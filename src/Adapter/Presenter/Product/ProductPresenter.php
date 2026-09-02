<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Presenter\Product;

use Hook;
use Language;
use Link;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\HookManager;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;
use PrestaShop\PrestaShop\Core\Product\ProductPresentationSettings;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProductPresenter
{
    /**
     * @var Configuration
     */
    protected $configuration;

    /**
     * @var HookManager
     */
    protected $hookManager;

    /**
     * @var ImageRetriever
     */
    protected $imageRetriever;

    /**
     * @var Link
     */
    protected $link;

    /**
     * @var PriceFormatter
     */
    protected $priceFormatter;

    /**
     * @var ProductColorsRetriever
     */
    protected $productColorsRetriever;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    public function __construct(
        ImageRetriever $imageRetriever,
        Link $link,
        PriceFormatter $priceFormatter,
        ProductColorsRetriever $productColorsRetriever,
        TranslatorInterface $translator,
        ?HookManager $hookManager = null,
        ?Configuration $configuration = null
    ) {
        $this->imageRetriever = $imageRetriever;
        $this->link = $link;
        $this->priceFormatter = $priceFormatter;
        $this->productColorsRetriever = $productColorsRetriever;
        $this->translator = $translator;
        $this->hookManager = $hookManager ?? new HookManager();
        $this->configuration = $configuration ?? new Configuration();
    }

    public function present(
        ProductPresentationSettings $settings,
        array $product,
        Language $language
    ) {
        $productLazyArray = new ProductLazyArray(
            $settings,
            $product,
            $language,
            $this->imageRetriever,
            $this->link,
            $this->priceFormatter,
            $this->productColorsRetriever,
            $this->translator,
            $this->hookManager,
            $this->configuration
        );

        Hook::exec('actionPresentProduct',
            ['presentedProduct' => &$productLazyArray]
        );

        return $productLazyArray;
    }
}
