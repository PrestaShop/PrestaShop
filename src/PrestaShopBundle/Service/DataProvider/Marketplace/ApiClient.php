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

namespace PrestaShopBundle\Service\DataProvider\Marketplace;

use GuzzleHttp\Client;
use PrestaShop\PrestaShop\Adapter\Addons\AddonsDataProvider;

class ApiClient
{
    /**
     * @var Client
     */
    private $addonsApiClient;

    /**
     * @var array<string, string>
     */
    private $queryParameters = [
        'format' => 'json',
    ];
    private $defaultQueryParameters;

    /**
     * @param Client $addonsApiClient
     * @param string $locale
     * @param string|false $isoCode
     * @param null $toolsAdapter
     * @param string $domain
     * @param string $shopVersion
     */
    public function __construct(
        Client $addonsApiClient,
        $locale,
        $isoCode,
        $toolsAdapter,
        $domain,
        $shopVersion
    ) {
        $this->addonsApiClient = $addonsApiClient;

        list($isoLang) = explode('-', $locale);

        $this->setIsoLang($isoLang)
            ->setIsoCode($isoCode)
            ->setVersion($shopVersion)
            ->setShopUrl($domain);
        $this->defaultQueryParameters = $this->queryParameters;
    }

    /**
     * @Deprecated use Client constructor instead
     */
    public function setSslVerification($verifySsl)
    {
    }

    /**
     * @param Client $client
     *
     * @return $this
     */
    public function setClient(Client $client)
    {
        $this->addonsApiClient = $client;

        return $this;
    }

    /**
     * In case you reuse the Client, you may want to clean the previous parameters.
     */
    public function reset()
    {
        $this->queryParameters = $this->defaultQueryParameters;
    }

    public function getCategories()
    {
        $response = $this->setMethod('listing')
            ->setAction('categories')
            ->getResponse();

        $responseArray = json_decode($response);

        return isset($responseArray->module) ? $responseArray->module : [];
    }

    /**
     * Call API for module ZIP content (= download).
     *
     * @param int $moduleId
     * @param string $moduleChannel
     *
     * @return string binary content (zip format)
     */
    public function getModuleZip($moduleId, string $moduleChannel = AddonsDataProvider::ADDONS_API_MODULE_CHANNEL_STABLE)
    {
        return $this->setMethod('module')
            ->setModuleId($moduleId)
            ->setModuleChannel($moduleChannel)
            ->getPostResponse();
    }

    public function getResponse()
    {
        return (string) $this->addonsApiClient
            ->get(
                '',
                ['query' => $this->queryParameters,
                ]
            )->getBody();
    }

    public function getPostResponse()
    {
        return (string) $this->addonsApiClient
            ->post(
                '',
                ['query' => $this->queryParameters,
                ]
            )->getBody();
    }

    /*
     * REQUEST PARAMETER SETTERS.
     * All parameters will have the same label as their function name.
     */

    public function setMethod($method)
    {
        $this->queryParameters['method'] = $method;

        return $this;
    }

    public function setAction($action)
    {
        $this->queryParameters['action'] = $action;

        return $this;
    }

    public function setIsoLang($isoLang)
    {
        $this->queryParameters['iso_lang'] = $isoLang;

        return $this;
    }

    public function setIsoCode($isoCode)
    {
        $this->queryParameters['iso_code'] = $isoCode;

        return $this;
    }

    public function setVersion($version)
    {
        $this->queryParameters['version'] = $version;

        return $this;
    }

    /**
     * @param string $moduleChannel
     *
     * @return self
     */
    public function setModuleChannel(string $moduleChannel): self
    {
        $this->queryParameters['channel'] = $moduleChannel;

        return $this;
    }

    public function setModuleId($moduleId)
    {
        $this->queryParameters['id_module'] = $moduleId;

        return $this;
    }

    public function setModuleKey($moduleKey)
    {
        $this->queryParameters['module_key'] = $moduleKey;

        return $this;
    }

    public function setModuleName($moduleName)
    {
        $this->queryParameters['module_name'] = $moduleName;

        return $this;
    }

    public function setShopUrl($shop_url)
    {
        $this->queryParameters['shop_url'] = $shop_url;

        return $this;
    }

    public function setUserMail($userMail)
    {
        $this->queryParameters['username'] = $userMail;

        return $this;
    }

    public function setPassword($password)
    {
        $this->queryParameters['password'] = $password;

        return $this;
    }

    /**
     * @return array<string, string>
     */
    public function getQueryParameters(): array
    {
        return $this->queryParameters;
    }
}
