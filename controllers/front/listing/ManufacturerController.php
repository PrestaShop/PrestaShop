<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
use PrestaShop\PrestaShop\Adapter\Manufacturer\ManufacturerProductSearchProvider;
use PrestaShop\PrestaShop\Adapter\Presenter\Manufacturer\ManufacturerPresenter;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;

class ManufacturerControllerCore extends ProductListingFrontController
{
    /** @var string */
    public $php_self = 'manufacturer';

    /** @var Manufacturer|null */
    protected $manufacturer;
    protected $label;

    /** @var ManufacturerPresenter */
    protected $manufacturerPresenter;

    public function canonicalRedirection(string $canonicalURL = ''): void
    {
        if (Validate::isLoadedObject($this->manufacturer)) {
            parent::canonicalRedirection($this->context->link->getManufacturerLink($this->manufacturer));
        } elseif ($canonicalURL) {
            parent::canonicalRedirection($canonicalURL);
        }
    }

    /**
     * Returns canonical URL for current manufacturer or a manufacturer list
     *
     * @return string
     */
    public function getCanonicalURL(): string
    {
        if (Validate::isLoadedObject($this->manufacturer)) {
            return $this->buildPaginatedUrl($this->context->link->getManufacturerLink($this->manufacturer));
        }

        return $this->context->link->getPageLink('manufacturer');
    }

    /**
     * Initialize manufaturer controller.
     *
     * @see FrontController::init()
     */
    public function init(): void
    {
        if ($id_manufacturer = Tools::getValue('id_manufacturer')) {
            $this->manufacturer = new Manufacturer((int) $id_manufacturer, $this->context->language->id);

            if (!Validate::isLoadedObject($this->manufacturer) || !$this->manufacturer->active || !$this->manufacturer->isAssociatedToShop()) {
                $this->redirect_after = '404';
                $this->redirect();
            } else {
                $this->canonicalRedirection();
            }
        }

        // Initialize presenter, we will use it for all cases
        $this->manufacturerPresenter = new ManufacturerPresenter($this->context->link);

        parent::init();
    }

    /**
     * Assign template vars related to page content.
     *
     * @see FrontController::initContent()
     */
    public function initContent(): void
    {
        if (Configuration::get('PS_DISPLAY_MANUFACTURERS')) {
            parent::initContent();

            if (Validate::isLoadedObject($this->manufacturer) && $this->manufacturer->active && $this->manufacturer->isAssociatedToShop()) {
                $this->assignManufacturer();
                $this->label = $this->trans(
                    'List of products by brand %brand_name%',
                    [
                        '%brand_name%' => $this->manufacturer->name,
                    ],
                    'Shop.Theme.Catalog'
                );
                $this->doProductSearch(
                    'catalog/listing/manufacturer',
                    ['entity' => 'manufacturer', 'id' => $this->manufacturer->id]
                );
            } else {
                $this->assignAll();
                $this->label = $this->trans(
                    'List of all brands',
                    [],
                    'Shop.Theme.Catalog'
                );
                $this->setTemplate('catalog/manufacturers', ['entity' => 'manufacturers']);
            }
        } else {
            $this->redirect_after = '404';
            $this->redirect();
        }
    }

    /**
     * Gets the product search query for the controller. This is a set of information that
     * a filtering module or the default provider will use to fetch our products.
     *
     * @return ProductSearchQuery
     *
     * @throws PrestaShop\PrestaShop\Core\Product\Search\Exception\InvalidSortOrderDirectionException
     */
    protected function getProductSearchQuery(): ProductSearchQuery
    {
        $query = new ProductSearchQuery();
        $query
            ->setQueryType('manufacturer')
            ->setIdManufacturer($this->manufacturer->id)
            ->setSortOrder(new SortOrder('product', Tools::getProductsOrder('by'), Tools::getProductsOrder('way')));

        return $query;
    }

    /**
     * Default product search provider used if no filtering module stood up for the job
     *
     * @return ManufacturerProductSearchProvider
     */
    protected function getDefaultProductSearchProvider(): ManufacturerProductSearchProvider
    {
        return new ManufacturerProductSearchProvider(
            $this->getTranslator(),
            $this->manufacturer
        );
    }

    /**
     * Assign template vars if displaying one manufacturer.
     */
    protected function assignManufacturer(): void
    {
        $manufacturerVar = $this->manufacturerPresenter->present(
            $this->manufacturer,
            $this->context->language
        );

        // Chained hook call - if multiple modules are hooked here, they will receive the result of the previous one as a parameter
        $filteredManufacturer = Hook::exec(
            'filterManufacturerContent',
            ['object' => $manufacturerVar],
            $id_module = null,
            $array_return = false,
            $check_exceptions = true,
            $use_push = false,
            $id_shop = null,
            $chain = true
        );
        if (!empty($filteredManufacturer['object'])) {
            $manufacturerVar = $filteredManufacturer['object'];
        }

        $this->context->smarty->assign([
            'manufacturer' => $manufacturerVar,
        ]);
    }

    /**
     * Assign template vars if displaying the manufacturer list.
     */
    protected function assignAll(): void
    {
        $manufacturersVar = $this->getTemplateVarManufacturers();

        if (!empty($manufacturersVar)) {
            foreach ($manufacturersVar as $k => $manufacturer) {
                // Chained hook call - if multiple modules are hooked here, they will receive the result of the previous one as a parameter
                $filteredManufacturer = Hook::exec(
                    'filterManufacturerContent',
                    ['object' => $manufacturer],
                    $id_module = null,
                    $array_return = false,
                    $check_exceptions = true,
                    $use_push = false,
                    $id_shop = null,
                    $chain = true
                );
                if (!empty($filteredManufacturer['object'])) {
                    $manufacturersVar[$k] = $filteredManufacturer['object'];
                }
            }
        }

        $this->context->smarty->assign([
            'brands' => $manufacturersVar,
        ]);
    }

    public function getTemplateVarManufacturers(): array
    {
        $manufacturers = Manufacturer::getManufacturers(true, $this->context->language->id);

        foreach ($manufacturers as &$manufacturer) {
            $manufacturer = $this->manufacturerPresenter->present(
                $manufacturer,
                $this->context->language
            );
        }

        return $manufacturers;
    }

    public function getListingLabel(): string
    {
        return $this->label;
    }

    public function getBreadcrumbLinks(): array
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $breadcrumb['links'][] = [
            'title' => $this->getTranslator()->trans('Brands', [], 'Shop.Theme.Global'),
            'url' => $this->context->link->getPageLink('manufacturer'),
        ];

        if (!empty($this->manufacturer)) {
            $breadcrumb['links'][] = [
                'title' => $this->manufacturer->name,
                'url' => $this->context->link->getManufacturerLink($this->manufacturer),
            ];
        }

        return $breadcrumb;
    }

    /**
     * Initializes a set of commonly used variables related to the current page, available for use
     * in the template. @see FrontController::assignGeneralPurposeVariables for more information.
     *
     * @return array
     */
    public function getTemplateVarPage(): array
    {
        $page = parent::getTemplateVarPage();

        if (!empty($this->manufacturer)) {
            $page['body_classes']['manufacturer-id-' . $this->manufacturer->id] = true;
            $page['body_classes']['manufacturer-' . $this->manufacturer->name] = true;
        }

        return $page;
    }

    /**
     * @return Manufacturer|null
     */
    public function getManufacturer(): ?Manufacturer
    {
        return $this->manufacturer;
    }
}
