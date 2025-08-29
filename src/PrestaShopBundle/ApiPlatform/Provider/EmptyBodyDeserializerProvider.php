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

namespace PrestaShopBundle\ApiPlatform\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\DependencyInjection\Attribute\AutowireDecorated;
use Symfony\Component\HttpFoundation\Request;

/**
 * This decorator is used when we enabled our custom property allowEmptyBody We don't need to specify
 * a content-type in this case but the DeserializerProvider forces it, so we decorate it and mimic the
 * expected behaviour.
 */
#[AsDecorator(decorates: 'api_platform.state_provider.deserialize')]
class EmptyBodyDeserializerProvider implements ProviderInterface
{
    public function __construct(
        #[AutowireDecorated]
        private readonly ProviderInterface $decorated,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($context['request'] instanceof Request) {
            $request = $context['request'];
            if (($operation->getExtraProperties()['allowEmptyBody'] ?? false) && empty((string) $request->getContent())) {
                $requestFormat = $request->getRequestFormat() ?: 'json';
                $mimeType = $request->getMimeType($requestFormat) ?: 'application/json';
                $request->headers->set('Content-Type', $mimeType);
                $request->attributes->set('input_format', $requestFormat);
            }
        }

        return $this->decorated->provide($operation, $uriVariables, $context);
    }
}
