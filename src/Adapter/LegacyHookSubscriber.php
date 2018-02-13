<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\PrestaShop\Adapter;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use PrestaShopBundle\Service\Hook\HookEvent;
use PrestaShopBundle\Service\Hook\RenderingHookEvent;
use Context;
use Hook;

/**
 * The subscriber for HookDispatcher that triggers legacy Hooks.
 *
 * This subscriber is registered into the HookDispatcher service via services.yml.
 * The legacy hooks are registered one by one in the dispatcher, but each corresponding
 * function is a magic method catched by __call().
 * This ensure the listeners' count is real.
 *
 *
 * <h3>COVERED HOOKS FROM PrestaShop v1.7</h3>
 *
 * <div>
 *   <h4>ProductController</h4>
 *   <ul>
 *     <li><h5>actionProductAdd</h5>
 *       <ul>
 *         <li>Legacy code (for legacy Admin controller, Product pages) is not modified</li>
 *         <li>Since 1.7 (refactored Admin Product pages), triggered by an adapter from legacy code: not modified</li>
 *         <li>Parameters:
 *           <ol>
 *             <li>product: [object] Product (unchanged)</li>
 *             <li>cookie: [object] Cookie (unchanged)</li>
 *             <li>cart: [object] Cart (unchanged)</li>
 *             <li>altern: ???</li>
 *           </ol>
 *         </li>
 *         <li>Returns: void</li>
 *       </ul>
 *     </li>
 *     <li><h5>actionProductDelete</h5>
 *       <ul>
 *         <li>Legacy code (for legacy Admin controller, Product pages) is not modified</li>
 *         <li>Since 1.7 (refactored Admin Product pages), triggered by an adapter from legacy code: not modified</li>
 *         <li>Parameters:
 *           <ol>
 *             <li>id_product: [int] Product ID to be deleted (unchanged)</li>
 *             <li>product: [object] Product (unchanged)</li>
 *             <li>cookie: [object] Cookie (unchanged)</li>
 *             <li>cart: [object] Cart (unchanged)</li>
 *             <li>altern: ???</li>
 *           </ol>
 *         </li>
 *         <li>Returns: void</li>
 *       </ul>
 *     </li>
 *     <li><h5>actionProductUpdate</h5>
 *       <ul>
 *         <li>Legacy code (for legacy Admin controller, Product pages) is not modified</li>
 *         <li>Since 1.7 (refactored Admin Product pages), triggered by an adapter from legacy code or directly from an adapter: duplicated behavior.</li>
 *         <li>Parameters:
 *           <ol>
 *             <li>id_product: [int] Product ID to be deleted (unchanged)</li>
 *             <li>product: [object] Product (unchanged)</li>
 *             <li>cookie: [object] Cookie (unchanged)</li>
 *             <li>cart: [object] Cart (unchanged)</li>
 *             <li>altern: ???</li>
 *           </ol>
 *         </li>
 *         <li>Returns: void</li>
 *       </ul>
 *     </li>
 *     <li><h5>actionAdminProductsListingFieldsModifier</h5>
 *       <ul>
 *         <li>Legacy code (for legacy Admin controller, Product pages) is not modified</li>
 *         <li>Since 1.7 (refactored Admin Product pages), triggered by an adapter with a DIFFERENT BEHAVIOR.</li>
 *         <li>Parameters for the new 1.7 behavior (if _ps_version present and >=1.7.0):
 *           <ol>
 *             <li>_ps_version: [int] value of _PS_VERSION_ is present only if triggered on new Product catalog page (>=1.7)</li>
 *             <li>sql_select: [&array] SELECT fields (to modify directly if needed)</li>
 *             <li>sql_table: [&array] TABLES to join (to modify directly if needed)</li>
 *             <li>sql_where: [&array] WHERE clauses (to modify directly if needed)</li>
 *             <li>sql_order: [&array] ORDER BY clauses (to modify directly if needed)</li>
 *             <li>sql_limit: [&string] LIMIT clause (to modify directly if needed)</li>
 *             <li>cookie: [object] Cookie (unchanged)</li>
 *             <li>cart: [object] Cart (unchanged)</li>
 *             <li>altern: ???</li>
 *           </ol>
 *         </li>
 *         <li>{@see AbstractAdminQueryBuilder::compileSqlQuery()} for more details about how to build these arrays.</li>
 *         <li>{@see AdminProductDataProvider::getCatalogProductList()} for an example.</li>
 *         <li>Returns: void (but you can modify input parameters passed by reference)</li>
 *       </ul>
 *     </li>
 *     <li><h5>actionAdminProductsListingResultsModifier</h5>
 *       <ul>
 *         <li>Legacy code (for legacy Admin controller, Product pages) is not modified</li>
 *         <li>Since 1.7 (refactored Admin Product pages), triggered by an adapter with a DIFFERENT BEHAVIOR.</li>
 *         <li>Parameters for the new 1.7 behavior (if _ps_version present and >=1.7.0):
 *           <ol>
 *             <li>_ps_version: [int] value of _PS_VERSION_ is present only if triggered on new Product catalog page (>=1.7)</li>
 *             <li>products: [&array] List of the products on the requested page, after sql query (modified by actionAdminProductsListingFieldsModifier hook)</li>
 *             <li>total: [integer] total count of products (without pagination) that matches with the requested filters</li>
 *             <li>cookie: [object] Cookie (unchanged)</li>
 *             <li>cart: [object] Cart (unchanged)</li>
 *             <li>altern: ???</li>
 *           </ol>
 *         </li>
 *         <li>{@see AdminProductDataProvider::getCatalogProductList()} for an example.</li>
 *         <li>Returns: void (but you can modify input parameters passed by reference)</li>
 *       </ul>
 *     </li>
 *     <li><h5>displayAdminProductsExtra</h5>
 *       <ul>
 *         <li>Legacy code (for legacy Admin controller, Product pages) is not modified</li>
 *         <li>Since 1.7 (refactored Admin Product pages), triggered by the new architecture in Twig template.</li>
 *         <li>Parameters for the new 1.7 behavior (if _ps_version present and >=1.7.0):
 *           <ol>
 *             <li>_ps_version: [int] value of _PS_VERSION_ is present only if triggered on new Product details page (>=1.7)</li>
 *             <li>id_product: [int] Product ID to be detailed in the form</li>
 *             <li>cookie: [object] Cookie (unchanged)</li>
 *             <li>cart: [object] Cart (unchanged)</li>
 *             <li>altern: ???</li>
 *           </ol>
 *         </li>
 *         <li>Returns: HTML code, all embedded. While the page has been refactored, JS and legacy HTML/CSS classes are all changed.</li>
 *       </ul>
 *     </li>
 *     <li><h5>actionUpdateQuantity</h5>
 *       <ul>
 *         <li>Legacy code (for legacy Admin controller, Product pages) is not modified</li>
 *         <li>Since 1.7 (refactored Admin Product pages), triggered by an adapter from legacy code or directly from an adapter: duplicated behavior.</li>
 *         <li>Parameters:
 *           <ol>
 *             <li>id_product: [int] Product ID to be updated (unchanged)</li>
 *             <li>id_product_attribute: [int] Product attribute ID to be updated (unchanged, 0 if the product has no attribute)</li>
 *             <li>quantity: [int] Quantity to set (unchanged)</li>
 *             <li>cookie: [object] Cookie (unchanged)</li>
 *             <li>cart: [object] Cart (unchanged)</li>
 *             <li>altern: ???</li>
 *           </ol>
 *         </li>
 *         <li>Returns: void</li>
 *       </ul>
 *     </li>
 *     <li><h5>action{Admin|AdminProductsController}{Duplicate|Delete|Sort|Activate|Deactivate}{Before|After}</h5>
 *       <ul>
 *         <li>Since 1.7, triggered from the Controller</li>
 *         <li>Parameter, one of these:
 *           <ol>
 *             <li>product_list_id: [array] A list of product IDs concerning the action</li>
 *             <li>product_id: [int] The product ID concerning the action</li>
 *             <li>product_list_position: [array] The positions of products in the product_list_id parameter, to sort</li>
 *           </ol>
 *         </li>
 *         <li>Returns: void</li>
 *       </ul>
 *     </li>
 *     <li><h5>shouldUseLegacyPage</h5>
 *       <ul>
 *         <li>Since 1.7, triggered from the Controller. This is a transitional behavior and can be removed in the future.</li>
 *         <li>Parameters:
 *           <ol>
 *             <li>page: [string] The page name concerning the parameter change ('product', etc...)</li>
 *             <li>use_legacy: [boolean] True if the user ask to use legacy page instead of the new one.</li>
 *           </ol>
 *         </li>
 *         <li>Returns: void</li>
 *       </ul>
 *     </li>
 *     <li><h5>actionProductActivation</h5>
 *       <ul>
 *         <li>Since 1.7, triggered from the AdminProductDataUpdater via sf ProductController.</li>
 *         <li>Parameters:
 *           <ol>
 *             <li>id_product: [int] Product ID to be updated</li>
 *             <li>product: [object] Product</li>
 *             <li>activated: [boolean] True if activation, False if deactivation.</li>
 *           </ol>
 *         </li>
 *         <li>Returns: void</li>
 *       </ul>
 *     </li>
 *   </ul>
 * </div>
 * <hr />
 */
class LegacyHookSubscriber implements EventSubscriberInterface
{
    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value are a function name
     * that will be solved by magic __call(). The function contains data to extract: hookId, moduleId
     *
     * TODO: add cache layer on $listeners
     *
     * @return array The listeners array
     */
    public static function getSubscribedEvents()
    {
        $listeners = array();

        //Hack SF2 cache clear : if context not mounted, bypass legacy call
        $legacyContext = Context::getContext();
        if (!$legacyContext || empty($legacyContext->shop) || empty($legacyContext->employee)) {
            return $listeners;
        }

        $hooks = Hook::getHooks();

        if (is_array($hooks)) {
            foreach ($hooks as $hook) {
                $name = $hook['name'];
                $id = $hook['id_hook'];

                $moduleListeners = array();
                $modules = array();
                //SF2 cache clear bug fix : call bqSQL alias function
                if (function_exists("bqSQL")) {
                    $modules = Hook::getHookModuleExecList($name);
                }

                if (is_array($modules)) {
                    foreach ($modules as $order => $module) {
                        $moduleId = $module['id_module'];
                        $functionName = 'call_' . $id . '_' . $moduleId;
                        $moduleListeners[] = array($functionName, 2000 - $order);
                    }

                    if (count($moduleListeners)) {
                        $listeners[$name] = $moduleListeners;
                    }
                }
            }
        }
        return $listeners;
    }

    /**
     * This will handle magic methods registered as listeners.
     *
     * These methods are built with the following syntax:
     * "call_<hookID>_<moduleID>(HookEvent $event, $hookName)"
     *
     * @param string $name The method called
     * @param array $args The HookEvent, and then the hook name (eventName)
     * @throws \BadMethodCallException
     */
    public function __call($name, $args)
    {
        if (strpos($name, 'call_') !== 0) {
            throw new \BadMethodCallException('The call to \''.$name.'\' is not recognized.');
        }

        $ids = explode('_', $name);
        array_shift($ids); // remove 'call'

        if (count($ids) != 2) {
            throw new \BadMethodCallException('The call to \''.$name.'\' is not recognized.');
        }

        $moduleId = $ids[1];

        $hookName = $args[1];
        $event = $args[0];
        /* @var $event HookEvent */
        $content = Hook::exec($hookName, $event->getHookParameters(), $moduleId, ($event instanceof RenderingHookEvent));

        if ($event instanceof RenderingHookEvent) {
            $event->setContent(array_values($content)[0], array_keys($content)[0]);
        }
    }
}
