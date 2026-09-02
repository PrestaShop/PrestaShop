<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Controller\Admin;

use PrestaShop\PrestaShop\Core\Domain\Configuration\Command\SwitchDebugModeCommand;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Manages Error pages (e.g. 500)
 */
class ErrorController extends PrestaShopAdminController
{
    /**
     * Enables debug mode from error page (500 for example)
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[AdminSecurity("is_granted('update', 'AdminPerformance') && is_granted('create', 'AdminPerformance') && is_granted('delete', 'AdminPerformance')")]
    public function enableDebugModeAction(Request $request): RedirectResponse
    {
        $this->dispatchCommand(new SwitchDebugModeCommand(true));

        return $this->redirect(
            $request->request->get('_redirect_url')
        );
    }

    public function showAction(Throwable $exception): Response
    {
        $flattenException = FlattenException::createFromThrowable($exception);
        $errorTemplate = match ($flattenException->getStatusCode()) {
            404 => '@PrestaShop/Admin/Error/error404.html.twig',
            403 => '@PrestaShop/Admin/Error/error403.html.twig',
            default => '@PrestaShop/Admin/Error/error.html.twig',
        };

        return $this->render($errorTemplate);
    }
}
