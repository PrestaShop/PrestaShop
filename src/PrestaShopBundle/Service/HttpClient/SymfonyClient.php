<?php

declare(strict_types=1);

namespace PrestaShopBundle\Service\HttpClient;

use Exception;
use PrestaShop\CircuitBreaker\Contract\ClientInterface;
use PrestaShop\CircuitBreaker\Exception\UnavailableServiceException;
use PrestaShop\CircuitBreaker\Exception\UnsupportedMethodException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SymfonyClient implements ClientInterface
{
    const DEFAULT_METHOD = 'GET';

    const SUPPORTED_METHODS = [
        'GET',
        'HEAD',
        'POST',
        'PUT',
        'PATCH',
        'DELETE',
        'OPTIONS',
    ];


    public function __construct(private readonly HttpClientInterface $client)
    {
    }

    /**
     * {@inheritdoc}
     *
     * @throws UnavailableServiceException
     */
    public function request(string $resource, array $options): string
    {
        try {

            $method = $this->getHttpMethod($options);
            $options['exceptions'] = true;
            return $this->client->request($method, $resource, $options)->getContent();
        } catch (Exception $e) {
            throw new UnavailableServiceException($e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    /**
     * @param array $options the list of options
     *
     * @return string the method
     *
     * @throws UnsupportedMethodException
     */
    private function getHttpMethod(array $options): string
    {
        if (isset($options['method'])) {
            if (!in_array($options['method'], self::SUPPORTED_METHODS)) {
                throw UnsupportedMethodException::unsupportedMethod($options['method']);
            }

            return $options['method'];
        }

        return self::DEFAULT_METHOD;
    }
}
