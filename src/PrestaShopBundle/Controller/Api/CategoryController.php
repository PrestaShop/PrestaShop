<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Controller\Api;

use PrestaShopBundle\Entity\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CategoryController extends ApiController
{
    /**
     * @var CategoryRepository
     */
    public $categoryRepository;

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function listCategoriesAction(Request $request)
    {
        return $this->jsonResponse($this->categoryRepository->getCategories(true), $request);
    }
}
