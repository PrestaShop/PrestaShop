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

namespace PrestaShopBundle\Controller\Admin;

use PrestaShopBundle\Service\Routing\Router as PrestaShopRouter;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Security warning controller
 */
class SecurityController extends PrestaShopAdminController
{
    public function __construct(
        private readonly Security $security,
        private readonly CsrfTokenManagerInterface $tokenManager,
        private readonly ValidatorInterface $validator,
        private readonly RouterInterface $router,
    ) {
    }

    public function compromisedAccessAction(Request $request): Response
    {
        $requestUri = urldecode($request->query->get('uri'));
        if (empty($requestUri)) {
            $requestUri = $this->router->generate('admin_homepage');
        }
        $url = new Assert\Url();
        $violations = $this->validator->validate($requestUri, [$url]);
        if ($violations->count()) {
            return $this->redirect('admin_homepage');
        }

        $newToken = $this->tokenManager
            ->getToken($this->security->getUser()->getUserIdentifier())
            ->getValue();

        $newUri = PrestaShopRouter::generateTokenizedUrl($requestUri, $newToken);

        return $this->render('@PrestaShop/Admin/Security/compromised.html.twig', [
            'requestUri' => $newUri,
        ]);
    }
}
