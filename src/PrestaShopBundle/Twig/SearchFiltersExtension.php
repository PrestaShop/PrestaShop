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
namespace PrestaShopBundle\Twig;

use Symfony\Component\HttpFoundation\RequestStack;
use PrestaShop\PrestaShop\Core\Search\ControllerAction;

/**
 * Improve information from request, get the real action and controller's names.
 */
final class SearchFiltersExtension extends \Twig_Extension
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('getController', [$this, 'getController']),
            new \Twig_SimpleFunction('getAction', [$this, 'getAction'])
        ];
    }

    /**
     * Get current controller name
     *
     * @return string
     */
    public function getController()
    {
        $request = $this->requestStack->getCurrentRequest();

        return ControllerAction::getControllerName($request->get('_controller'));
    }

    /**
     * Get current action name
     *
     * @return string
     */
    public function getAction()
    {
        $request = $this->requestStack->getCurrentRequest();

        return ControllerAction::getActionName($request->get('_controller'));
    }
}
