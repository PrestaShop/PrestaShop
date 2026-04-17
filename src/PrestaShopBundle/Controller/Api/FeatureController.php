<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Controller\Api;

use PrestaShopBundle\Entity\Repository\FeatureAttributeRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class FeatureController extends ApiController
{
    /**
     * @var FeatureAttributeRepository
     */
    public $featureAttributeRepository;

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function listFeaturesAction(Request $request)
    {
        return $this->jsonResponse($this->featureAttributeRepository->getFeatures(), $request);
    }
}
