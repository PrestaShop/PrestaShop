<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
