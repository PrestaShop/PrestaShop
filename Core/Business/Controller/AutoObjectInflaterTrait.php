<?php
/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\PrestaShop\Core\Business\Controller;

use PrestaShop\PrestaShop\Core\Foundation\Routing\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * This Trait will add convenience hooks before controller action execution,
 * and will try to inflate data from DB if the route contains parameters (if their name follow a specific norm)
 * and will insert them in $response->getContentData() set.
 *
 * The route.yml should indicate an object id and its class to be detected by this Trait like this:
 * 'id_<RepositoryName>'
 * example:
 * my_route:
 *   path:     /my_path/{id_order}/{id_product}/{id_customer}/view
 *   defaults: { _controller: 'Admin\TestController::bAction', id_order: 1 }
 *
 */
trait AutoObjectInflaterTrait
{
    /**
     * This trait helper will try to identify some elements in the uri parameters (if well set in the routes*.yml files,
     * and in the right syntax), to query them in the database, and to complete $request->attributes with the
     * found object.
     *
     * @param Request $request
     * @param Response $response
     * @return boolean True if success; False to forbid action execution
     */
    public function beforeActionInflateRequestedObjects(Request &$request, Response &$response)
    {
        foreach ($request->attributes->all() as $key => $value) {

            // Find parameters that begins with id_ to try to inflate corresponding object
            if (strpos($key, 'id_') === 0) {
                $className = ucfirst(substr($key, 3));
                if (!class_exists($className)) {
                    continue;
                }
                $class = new \ReflectionClass($className);
                $constructorParameters = $class->getConstructor()->getParameters();
                $constructorParametersValues = array();
                
                foreach ($constructorParameters as $p) {
                    /* @var $p \ReflectionParameter */
                    if ($p->name == 'id') {
                        $constructorParametersValues[] = $value;
                    }
                    // TODO : search for other default params... (id_lang, id_shop or shop_id, limit, offset, list, etc...)
                }

                $object = $class->newInstanceArgs($constructorParametersValues);
                if (\Validate::isLoadedObject($object)) { // FIXME: this test should be in the new Archi, or in an Adapter.
                    $response->addContentData($className, $object);
                } else {
                    // To indicate we tried, but not found.
                    $response->addContentData($className, null);
                }
            }
        }
        return true;
    }
}
