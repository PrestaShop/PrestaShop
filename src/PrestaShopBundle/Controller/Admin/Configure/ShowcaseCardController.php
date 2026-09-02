<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Controller\Admin\Configure;

use Exception;
use PrestaShop\PrestaShop\Core\Domain\ShowcaseCard\Command\CloseShowcaseCardCommand;
use PrestaShop\PrestaShop\Core\Domain\ShowcaseCard\Exception\InvalidShowcaseCardNameException;
use PrestaShop\PrestaShop\Core\Domain\ShowcaseCard\ValueObject\ShowcaseCard;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use PrestaShopBundle\Security\Attribute\DemoRestricted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ShowcaseCardController extends PrestaShopAdminController
{
    /**
     * Saves the user preference of closing the showcase card.
     *
     * This action should be performed via POST, and expects two parameters:
     * - int $close=1
     * - string $name Name of the showcase card to close
     *
     * @see ShowcaseCard
     *
     * @return JsonResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_metas_index')]
    #[AdminSecurity("is_granted('create', 'CONFIGURE') && is_granted('update', 'CONFIGURE')")]
    public function closeShowcaseCardAction(Request $request): JsonResponse
    {
        // check prerequisites
        if (!$request->isMethod('post') || !$request->request->get('close')) {
            return $this->json(
                [
                    'success' => false,
                    'message' => '',
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $closeShowcaseCard = new CloseShowcaseCardCommand($this->getEmployeeContext()->getEmployee()->getId(), $request->request->get('name'));
            $this->dispatchCommand($closeShowcaseCard);

            return $this->json(
                [
                    'success' => true,
                    'message' => '',
                ]
            );
        } catch (Exception $e) {
            return $this->json(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
                ($e instanceof InvalidShowcaseCardNameException) ? Response::HTTP_BAD_REQUEST : Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
