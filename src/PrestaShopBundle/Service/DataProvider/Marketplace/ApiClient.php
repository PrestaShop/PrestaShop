<?php
/**
 * 2007-2016 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 *  @author 	PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2015 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShopBundle\Service\DataProvider\Marketplace;

use GuzzleHttp\Client;

class ApiClient
{
    private $addonsApiClient;
    private $queryParameters = array(
        'format' => 'json',
    );

    public function __construct(
        Client $addonsApiClient,
        $isoLang,
        $isoCode,
        $version
    ) {
        $this->addonsApiClient = $addonsApiClient;

        $this->setIsoLang($isoLang)
            ->setIsoCode($isoCode)
            ->setVersion($version)
        ;
    }

    public function getNativesModules()
    {
        return $this->setMethod('listing')
            ->setAction('native')
            ->getResponse()
        ;
    }

    public function getPreInstalledModules()
    {
        return $this->setMethod('listing')
            ->setAction('install-modules')
            ->getResponse()
        ;
    }

    public function getMustHaveModules()
    {
        return $this->setMethod('listing')
            ->setAction('must-have')
            ->getResponse()
        ;
    }

    public function getServices()
    {
        return $this->setMethod('listing')
            ->setAction('service')
            ->getResponse()
        ;
    }

    public function getCategories()
    {
        return $this->setMethod('listing')
            ->setAction('categories')
            ->getResponse()
        ;
    }

    public function getModule($moduleId)
    {
        return $this->setMethod('listing')
            ->setAction('module')
            ->getResponse()
        ;
    }

    public function getResponse()
    {
        return (string) $this->addonsApiClient
            ->request('GET', '/',
                array('query' => $this->queryParameters,
                )
            )->getBody()
        ;
    }

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
}
