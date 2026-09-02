<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Controller\Api;

use PrestaShopBundle\Entity\Repository\ManufacturerRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ManufacturerController extends ApiController
{
    /**
     * @var ManufacturerRepository
     */
    public $manufacturerRepository;

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function listManufacturersAction(Request $request)
    {
        return $this->jsonResponse($this->manufacturerRepository->getManufacturers(), $request);
    }
}
