<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Controller\Api\OAuth2;

use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Http\Message\ResponseFactoryInterface;
use Symfony\Bridge\PsrHttpMessage\HttpFoundationFactoryInterface;
use Symfony\Bridge\PsrHttpMessage\HttpMessageFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class responsible for handling access token requests
 *
 * @experimental
 */
class AccessTokenController extends AbstractController
{
    /**
     * @var AuthorizationServer
     */
    private $authorizationServer;

    /**
     * @var HttpMessageFactoryInterface
     */
    private $httpMessageFactory;

    /**
     * @var HttpFoundationFactoryInterface
     */
    private $httpFoundationFactory;

    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;

    public function __construct(
        AuthorizationServer $authorizationServer,
        HttpMessageFactoryInterface $httpMessageFactory,
        HttpFoundationFactoryInterface $httpFoundationFactory,
        ResponseFactoryInterface $responseFactory
    ) {
        $this->authorizationServer = $authorizationServer;
        $this->httpMessageFactory = $httpMessageFactory;
        $this->httpFoundationFactory = $httpFoundationFactory;
        $this->responseFactory = $responseFactory;
    }

    public function __invoke(Request $request): Response
    {
        $request = $this->httpMessageFactory->createRequest($request);
        $psr7Response = $this->responseFactory->createResponse();

        try {
            $response = $this->authorizationServer->respondToAccessTokenRequest($request, $psr7Response);
        } catch (OAuthServerException $exception) {
            $response = $exception->generateHttpResponse($psr7Response);
        }

        return $this->httpFoundationFactory->createResponse($response);
    }
}
