<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace Tests\Resources\Controller;

use PrestaShopBundle\Security\Attribute\AdminSecurity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Class is used to test #[AdminSecurity()] attribute linter
 */
#[Route('/tests')]
class TestController extends AbstractController
{
    #[AdminSecurity(new Expression('is_granted("ROLE_UNKNOWN")'))]
    #[Route('/', name: 'test_index')]
    public function indexAction()
    {
        return new Response();
    }

    #[Route('/create', name: 'test_create')]
    public function createAction()
    {
        return new Response();
    }

    #[AdminSecurity(new Expression('is_granted("ROLE_EMPLOYEE")'))]
    #[Route('/something-complex', name: 'test_something_complex')]
    public function doSomethingComplexAction()
    {
        return new Response('ComplexAction');
    }

    #[AdminSecurity(new Expression('is_granted("ROLE_UNKNOWN")'), redirectRoute: 'test_something_complex')]
    #[Route('/test-redirect', name: 'test_redirect')]
    public function doRedirectIfForbidden(): Response
    {
        return new Response();
    }
}
