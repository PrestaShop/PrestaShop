<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
