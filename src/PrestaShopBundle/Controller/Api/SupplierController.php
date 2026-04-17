<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Controller\Api;

use PrestaShopBundle\Entity\Repository\SupplierRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SupplierController extends ApiController
{
    /**
     * @var SupplierRepository
     */
    public $supplierRepository;

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function listSuppliersAction(Request $request)
    {
        return $this->jsonResponse($this->supplierRepository->getSuppliers(), $request);
    }
}
