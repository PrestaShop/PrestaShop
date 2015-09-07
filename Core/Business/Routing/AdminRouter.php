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
namespace PrestaShop\PrestaShop\Core\Business\Routing;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Config\ConfigCacheFactory;
use Symfony\Component\Config\ConfigCacheInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;
use PrestaShop\PrestaShop\Core\Foundation\Controller\BaseController;
use PrestaShop\PrestaShop\Core\Foundation\Routing\Response;
use PrestaShop\PrestaShop\Core\Business\Routing\Router;
use PrestaShop\PrestaShop\Core\Business\Controller\AdminController;
use PrestaShop\PrestaShop\Core\Foundation\Exception\DevelopmentErrorException;

class AdminRouter extends Router
{
    /**
     * @var AdminRouter
     */
    private static $instance = null;

    /**
     * Get current instance of router (singleton)
     *
     * @return AdminRouter
     */
    final public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self('admin_routes(_(.*))?\.yml');
        }
        return self::$instance;
    }

    final protected function checkControllerAuthority(\ReflectionClass $class)
    {
        if (!$class->isSubclassOf('PrestaShop\\PrestaShop\\Core\\Business\\Controller\\AdminController')) {
            throw new DevelopmentErrorException('Admin router tried to call a non-admin controller ('.$class->name.'). Please verify your routes Settings, and controllers.');
        }
    }
}
