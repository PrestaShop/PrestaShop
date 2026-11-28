<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Stock;

use Access;
use Combination;
use Configuration;
use Context;
use DateTime;
use Employee;
use Exception;
use Mail;
use Pack;
use PrestaShop\PrestaShop\Adapter\LegacyContext as ContextAdapter;
use PrestaShop\PrestaShop\Adapter\Product\Stock\Update\StockQuantityUpdater;
use PrestaShop\PrestaShop\Adapter\ServiceLocator;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use PrestaShop\PrestaShop\Core\Mutation\MutationTracker;
use PrestaShopBundle\Entity\MutationAction;
use PrestaShopBundle\Entity\StockMvt;
use PrestaShopException;
use Product;
use StockAvailable;

/**
 * Class StockManager Refactored features about product stocks.
 */
class StockManager
{
    /**
     * The tracker is optional because this class is still instantiated without the
     * container in some legacy paths (e.g. OrderHistory); no mutation is recorded there.
     */
    public function __construct(
        private readonly ?MutationTracker $mutationTracker = null,
    ) {
    }

    /**
     * This will update a Pack quantity and will decrease the quantity of containing Products if needed.
     *
     * @deprecated Since 9.3.0 and will be removed in 10.0.0, use StockQuantityUpdater::increaseQuantity() or decreaseQuantity() instead.
     *
     * @param Product $product A product pack object to update its quantity
     * @param StockAvailable $stock_available the stock of the product to fix with correct quantity
     * @param int $delta_quantity The movement of the stock (negative for a decrease)
     * @param int|null $id_shop Optional shop ID
     */
    public function updatePackQuantity($product, $stock_available, $delta_quantity, $id_shop = null)
    {
        $this->getQuantityUpdater()->applyDeltaToPack(
            $product,
            $stock_available,
            (int) $delta_quantity,
            null === $id_shop ? null : (int) $id_shop
        );
    }

    /**
     * This will decrease (if needed) Packs containing this product
     * (with the right combination) if there is not enough product in stocks.
     *
     * @deprecated Since 9.3.0 and will be removed in 10.0.0, use StockQuantityUpdater::increaseQuantity() or decreaseQuantity() instead.
     *
     * @param Product $product A product object to update its quantity
     * @param int $id_product_attribute The product attribute to update
     * @param StockAvailable $stock_available the stock of the product to fix with correct quantity
     * @param int|null $id_shop Optional shop ID
     */
    public function updatePacksQuantityContainingProduct($product, $id_product_attribute, $stock_available, $id_shop = null)
    {
        $this->getQuantityUpdater()->capPacksContaining(
            $product,
            null === $id_product_attribute ? null : (int) $id_product_attribute,
            $stock_available,
            null === $id_shop ? null : (int) $id_shop
        );
    }

    /**
     * Will update Product available stock int he given combination. If product is a Pack, could decrease the sub products.
     * If Product is contained in a Pack, Pack could be decreased or not (only if sub product stocks become not sufficient).
     *
     * @deprecated Since 9.3.0 and will be removed in 10.0.0, use StockQuantityUpdater::increaseQuantity() or decreaseQuantity() instead.
     *
     * @param Product $product The product to update its stockAvailable
     * @param int|null $id_product_attribute The combination to update (null if not)
     * @param int $delta_quantity The quantity change (positive or negative)
     * @param int|null $id_shop Optional
     * @param bool $add_movement Optional
     * @param array $params Optional
     */
    public function updateQuantity($product, $id_product_attribute, $delta_quantity, $id_shop = null, $add_movement = false, $params = [])
    {
        $this->getQuantityUpdater()->applyDeltaToProduct(
            $product,
            null === $id_product_attribute ? null : (int) $id_product_attribute,
            (int) $delta_quantity,
            null === $id_shop ? null : (int) $id_shop,
            true === $add_movement,
            $params
        );
    }

    /**
     * @internal
     *
     * @param Product $product
     * @param int|null $id_product_attribute
     * @param int $newQuantity
     */
    public function sendLowStockAlertIfNeeded($product, $id_product_attribute, $newQuantity): void
    {
        if ($this->checkIfMustSendLowStockAlert($product, $id_product_attribute, $newQuantity)) {
            $this->sendLowStockAlert($product, $id_product_attribute, $newQuantity);
        }
    }

    private function getQuantityUpdater(): StockQuantityUpdater
    {
        return ServiceLocator::get(StockQuantityUpdater::class);
    }

    /**
     * @param Product $product
     * @param int $id_product_attribute
     * @param int $newQuantity
     *
     * @return bool
     */
    protected function checkIfMustSendLowStockAlert($product, $id_product_attribute, $newQuantity)
    {
        if (!Configuration::get('PS_STOCK_MANAGEMENT')) {
            return false;
        }

        // Do not send mail if multiples product are created / imported.
        if (defined('PS_MASS_PRODUCT_CREATION')) {
            return false;
        }

        $productHasAttributes = $product->hasAttributes();
        if ($productHasAttributes && $id_product_attribute) {
            $combination = new Combination($id_product_attribute);

            return $this->isCombinationQuantityUnderAlertThreshold($combination, $newQuantity);
        } elseif (!$productHasAttributes && !$id_product_attribute) {
            return $this->isProductQuantityUnderAlertThreshold($product, $newQuantity);
        }

        return false;
    }

    /**
     * @param Product $product
     * @param int $newQuantity
     *
     * @return bool
     */
    protected function isProductQuantityUnderAlertThreshold($product, $newQuantity)
    {
        // low_stock_threshold empty to disable (can be negative, null or zero)
        if ($product->low_stock_alert
            && $product->low_stock_threshold !== ''
            && $product->low_stock_threshold !== null
            && $newQuantity <= (int) $product->low_stock_threshold
        ) {
            return true;
        }

        return false;
    }

    /**
     * @param Combination $combination
     * @param int $newQuantity
     *
     * @return bool
     */
    protected function isCombinationQuantityUnderAlertThreshold(Combination $combination, $newQuantity)
    {
        // low_stock_threshold empty to disable (can be negative, null or zero)
        if ($combination->low_stock_alert
            && $combination->low_stock_threshold !== ''
            && $combination->low_stock_threshold !== null
            && $newQuantity <= (int) $combination->low_stock_threshold
        ) {
            return true;
        }

        return false;
    }

    /**
     * @param Product $product
     * @param int $id_product_attribute
     * @param int $newQuantity
     *
     * @throws Exception
     * @throws PrestaShopException
     */
    protected function sendLowStockAlert($product, $id_product_attribute, $newQuantity)
    {
        $context = Context::getContext();
        $idShop = (int) $context->shop->id;
        $idLang = (int) $context->language->id;
        $configuration = Configuration::getMultiple(
            [
                'MA_LAST_QTIES',
                'PS_STOCK_MANAGEMENT',
                'PS_SHOP_EMAIL',
                'PS_SHOP_NAME',
            ],
            null,
            null,
            $idShop
        );
        $productName = Product::getProductName($product->id, $id_product_attribute, $idLang);
        if ($id_product_attribute) {
            $combination = new Combination($id_product_attribute);
            $lowStockThreshold = $combination->low_stock_threshold;
        } else {
            $lowStockThreshold = $product->low_stock_threshold;
        }
        $templateVars = [
            '{qty}' => $newQuantity,
            '{product_id}' => $product->id,
            '{product_attribute_id}' => $id_product_attribute,
            '{product_reference}' => $product->reference,
            '{last_qty}' => $lowStockThreshold,
            '{product}' => $productName,
        ];

        // send email to every employee who have permission for this
        foreach (Employee::getEmployees() as $employeeData) {
            $employee = new Employee($employeeData['id_employee']);

            if (Access::isGranted('ROLE_MOD_TAB_ADMINSTOCKMANAGEMENT_READ', $employee->id_profile)) {
                $templateVars['{firstname}'] = $employee->firstname;
                $templateVars['{lastname}'] = $employee->lastname;

                Mail::Send(
                    $idLang,
                    'productoutofstock',
                    $context->getTranslator()->trans(
                        'Product out of stock',
                        [],
                        'Emails.Subject',
                        $context->language->locale
                    ),
                    $templateVars,
                    $employee->email,
                    null,
                    (string) $configuration['PS_SHOP_EMAIL'],
                    (string) $configuration['PS_SHOP_NAME'],
                    null,
                    null,
                    __DIR__ . '/mails/',
                    false,
                    $idShop
                );
            }
        }
    }

    /**
     * Public method to save a Movement.
     *
     * @param int $productId
     * @param int $productAttributeId
     * @param int $deltaQuantity
     * @param array $params
     *
     * @return bool
     */
    public function saveMovement($productId, $productAttributeId, $deltaQuantity, $params = [])
    {
        if ($deltaQuantity == 0) {
            return false;
        }

        $stockMvt = $this->prepareMovement($productId, $productAttributeId, $deltaQuantity, $params);
        if (!$stockMvt) {
            return false;
        }

        $sfContainer = SymfonyContainer::getInstance();
        if (null === $sfContainer) {
            return false;
        }

        $stockMvtRepository = $sfContainer->get('prestashop.core.api.stock_movement.repository');
        $stockMovementId = $stockMvtRepository->saveStockMvt($stockMvt);

        if ($stockMovementId) {
            // Records which API client created the movement (no-op when the request is not
            // authenticated with an API client), since StockMvt has no api client relation
            $this->mutationTracker?->addMutationForApiClient('stock_mvt', (int) $stockMovementId, MutationAction::CREATE);
        }

        return $stockMovementId;
    }

    /**
     * Prepare a Movement for registration.
     *
     * @param int $productId
     * @param int $productAttributeId
     * @param int $deltaQuantity
     * @param array $params
     *
     * @return bool|StockMvt
     */
    private function prepareMovement($productId, $productAttributeId, $deltaQuantity, $params = [])
    {
        $product = new Product($productId);

        if ($product->id) {
            $combinationAdapter = ServiceLocator::get('\\PrestaShop\\PrestaShop\\Adapter\\Combination');

            if ($productAttributeId > 0 && !$combinationAdapter->existsInDatabase((int) $productAttributeId)) {
                return false;
            }

            $stockManager = ServiceLocator::get('\\PrestaShop\\PrestaShop\\Adapter\\StockManager');
            $stockAvailable = $stockManager->getStockAvailableByProduct($product, $productAttributeId, $params['id_shop'] ?? null);

            if ($stockAvailable->id) {
                $stockMvt = new StockMvt();

                $stockMvt->setIdStock((int) $stockAvailable->id);

                if (!empty($params['id_order'])) {
                    $stockMvt->setIdOrder((int) $params['id_order']);
                }

                if (!empty($params['id_stock_mvt_reason'])) {
                    $stockMvt->setIdStockMvtReason((int) $params['id_stock_mvt_reason']);
                }

                $stockMvt->setSign($deltaQuantity >= 1 ? 1 : -1);
                $stockMvt->setPhysicalQuantity(abs($deltaQuantity));

                $stockMvt->setDateAdd(new DateTime());

                $employee = (new ContextAdapter())->getContext()->employee;
                // The context may hold an employee without an id (e.g. when the stock movement is
                // triggered outside the back office, like from the Admin API), keep the default 0.
                if (!empty($employee) && !empty($employee->id)) {
                    $stockMvt->setIdEmployee((int) $employee->id);
                    $stockMvt->setEmployeeFirstname($employee->firstname);
                    $stockMvt->setEmployeeLastname($employee->lastname);
                }

                return $stockMvt;
            }
        }

        return false;
    }
}
