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

declare(strict_types=1);

namespace PrestaShopBundle\EventListener;

use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\State\Util\OperationRequestInitiatorTrait;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * We have to implement this extra security listener because ApiPlatform AccessDeniedListener is only called
 * AFTER ReadListener so the whole code of the provider is always executed before the security check is performed
 * this is particularly sensitive when the provider is used to delete something or to perform an action. But
 * even read operation should be blocked early if the API Client has no permission over them.
 *
 * So we do have to use some ApiPlatform internal tools that are not meant to be used outside the framework, but
 * if security was handled correctly by the framework we wouldn't need to do this.
 */
class ScopeCheckerListener
{
    use OperationRequestInitiatorTrait;

    public function __construct(
        ResourceMetadataCollectionFactoryInterface $resourceMetadataCollectionFactory = null,
        private readonly Security $security,
    ) {
        $this->resourceMetadataCollectionFactory = $resourceMetadataCollectionFactory;
    }

    /**
     * Get the operation from the request (if it is a request associated to an ApiPlatform operation).
     * Check if some scopes were specified in the extraParameters, transform them into a security
     * expression understandable by the Security component and check if the operation is granted,
     * else throw an AccessDeniedException.
     *
     * @param RequestEvent $event
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $operation = $this->initializeOperation($request);
        if (!$operation) {
            return;
        }

        $operationsScopes = $operation->getExtraProperties()['scopes'] ?? [];
        if (empty($operationsScopes)) {
            return;
        }

        $scopesSecurityRule = '';
        $arrayLength = count($operationsScopes);
        foreach ($operationsScopes as $key => $scope) {
            $scopesSecurityRule .= 'is_granted("ROLE_' . strtoupper($scope) . '")';
            if ($key !== $arrayLength - 1) {
                $scopesSecurityRule .= ' OR ';
            }
        }

        if (!$this->security->isGranted(new Expression($scopesSecurityRule))) {
            throw new AccessDeniedException('Invalid scope.');
        }
    }
}
