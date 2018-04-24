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

namespace PrestaShopBundle\Controller\ArgumentResolver;

use PrestaShop\PrestaShop\Core\Search\ControllerAction;
use PrestaShop\PrestaShop\Core\Search\SearchParameters;
use PrestaShopBundle\Entity\Repository\AdminFilterRepository;
use PrestaShopBundle\Security\Admin\Employee;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\Request;
use PrestaShop\PrestaShop\Core\Search\Filters;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;

/**
 * If an action inject instance of Filters, this class is responsible of
 * creating it from available sources.
 */
class SearchParametersResolver implements ArgumentValueResolverInterface
{
    /**
     * @var SearchParameters
     */
    private $searchParameters;

    /**
     * @var AdminFilterRepository
     */
    private $adminFilterRepository;

    /**
     * @var Employee|void
     */
    private $employee;

    /**
     * @var int
     */
    private $shopId;

    /**
     * SearchParametersResolver constructor.
     *
     * @param SearchParameters      $searchParameters
     * @param TokenStorageInterface $tokenStorage
     * @param AdminFilterRepository $adminFilterRepository
     * @param int                   $shopId           The Shop id
     */
    public function __construct(
        SearchParameters $searchParameters,
        TokenStorageInterface $tokenStorage,
        AdminFilterRepository $adminFilterRepository,
        $shopId
    )
    {
        $this->searchParameters = $searchParameters;
        $this->adminFilterRepository = $adminFilterRepository;
        $this->employee = $this->getEmployee($tokenStorage);
        $this->shopId = $shopId;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Request $request, ArgumentMetadata $argument)
    {
        return is_subclass_of($argument->getType(), Filters::class) &&
            $this->employee instanceof Employee
        ;
    }

    /**
     * {@inheritdoc}
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        $filtersClass = $argument->getType();
        list($controller, $action) = ControllerAction::fromString($request->get('_controller'));
        // is the url contains filters?
        if ($request->query->has('filters')) {
            $filters = $this->searchParameters->getFiltersFromRequest($request, $filtersClass);

            $this->adminFilterRepository->createOrUpdateByEmployeeAndRouteParams(
                $this->employee->getId(),
                $this->shopId,
                $filters->all(),
                $controller,
                $action
            );
        } else {
            // do we have a saved search in DB?
            if ($request->isMethod('GET')) {
                $filters = $this->searchParameters->getFiltersFromRepository(
                    $this->employee->getId(),
                    $this->shopId,
                    $controller,
                    $action,
                    $filtersClass
                );
            }

            if (empty($filters)) {
                $filters = new $filtersClass($filtersClass::getDefaults());
            }
        }

        yield $filters;
    }

    /**
     * @param TokenStorageInterface $tokenStorage
     *
     * @return Employee|void
     */
    private function getEmployee(TokenStorageInterface $tokenStorage)
    {
        if (null === $token = $tokenStorage->getToken()) {
            return;
        }

        if (!is_object($employee = $token->getUser())) {
            return;
        }

        return $employee;
    }
}
