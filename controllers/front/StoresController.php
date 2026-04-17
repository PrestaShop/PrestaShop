<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
use PrestaShop\PrestaShop\Adapter\Presenter\Store\StorePresenter;

class StoresControllerCore extends FrontController
{
    /** @var string */
    public $php_self = 'stores';

    /** @var StorePresenter */
    protected $storePresenter;

    /**
     * Initialize stores controller.
     *
     * @see FrontController::init()
     */
    public function init(): void
    {
        // Initialize presenter, we will use it for all cases
        $this->storePresenter = new StorePresenter(
            $this->context->link,
            $this->context->getTranslator()
        );

        parent::init();
    }

    /**
     * Assign template vars related to page content.
     *
     * @see FrontController::initContent()
     */
    public function initContent(): void
    {
        $distance_unit = Configuration::get('PS_DISTANCE_UNIT');
        if (!in_array($distance_unit, ['km', 'mi'])) {
            $distance_unit = 'km';
        }

        // Load stores and present them
        $stores = $this->getTemplateVarStores();

        // If no stores are configured, we hide this page
        if (!empty($stores)) {
            $this->context->smarty->assign([
                'mediumSize' => Image::getSize(ImageType::getFormattedName('medium')),
                'searchUrl' => $this->context->link->getPageLink('stores'),
                'distance_unit' => $distance_unit,
                'stores' => $stores,
            ]);
            parent::initContent();
            $this->setTemplate('cms/stores');
        } else {
            $this->redirect_after = '404';
            $this->redirect();
        }
    }

    public function getTemplateVarStores(): array
    {
        $stores = Store::getStores($this->context->language->id);

        foreach ($stores as &$store) {
            $store = $this->storePresenter->present(
                $store,
                $this->context->language
            );
        }

        return $stores;
    }

    public function getBreadcrumbLinks(): array
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = [
            'title' => $this->trans('Our stores', [], 'Shop.Theme.Global'),
            'url' => $this->context->link->getPageLink('stores'),
        ];

        return $breadcrumb;
    }

    /**
     * {@inheritdoc}
     */
    public function getCanonicalURL(): string
    {
        return $this->context->link->getPageLink('stores');
    }
}
