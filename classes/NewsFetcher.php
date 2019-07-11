<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

use Doctrine\Common\Cache\FilesystemCache;
use GuzzleHttp\Message\Request;
use GuzzleHttp\Subscriber\Cache\CacheStorage;
use GuzzleHttp\Subscriber\Cache\CacheSubscriber;
use PrestaShop\CircuitBreaker\AdvancedCircuitBreakerFactory;
use PrestaShop\CircuitBreaker\Contract\FactoryInterface;
use PrestaShop\CircuitBreaker\FactorySettings;
use PrestaShop\CircuitBreaker\Storage\DoctrineCache;

class NewsFetcherCore
{
    const NUM_ARTICLES = 2;

    const CACHE_DURATION = 86400; // 24 hours

    const CLOSED_ALLOWED_FAILURES = 3;
    const CLOSED_TIMEOUT_SECONDS = 3;

    const OPEN_ALLOWED_FAILURES = 3;
    const OPEN_TIMEOUT_SECONDS = 3;
    const OPEN_THRESHOLD_SECONDS = 86400; // 24 hours

    /** @var array */
    private $blogSettings;

    /** @var FactoryInterface */
    private $factory;

    /** @var string */
    private $isoCode;

    /**
    * @param string $isoCode
    */
    public function __construct(
        string $isoCode
    ) {
        $this->isoCode = $isoCode;

        // Doctrine cache used for Guzzle and CircuitBreaker storage
        $doctrineCache = new FilesystemCache(_PS_CACHE_DIR_ . '/dashboard_news');

        // Init Guzzle cache
        $cacheStorage = new CacheStorage($doctrineCache, null, self::CACHE_DURATION);
        $cacheSubscriber = new CacheSubscriber($cacheStorage, function (Request $request) { return true; });

        // Init circuit breaker factory
        $storage = new DoctrineCache($doctrineCache);
        $this->blogSettings = new FactorySettings(self::CLOSED_ALLOWED_FAILURES, self::CLOSED_TIMEOUT_SECONDS, 0);
        $this->blogSettings
            ->setThreshold(self::OPEN_THRESHOLD_SECONDS)
            ->setStrippedFailures(self::OPEN_ALLOWED_FAILURES)
            ->setStrippedTimeout(self::OPEN_TIMEOUT_SECONDS)
            ->setStorage($storage)
            ->setClientOptions([
                'method' => 'GET',
                'subscribers' => [$cacheSubscriber],
            ])
        ;
        $this->factory = new AdvancedCircuitBreakerFactory();
    }

    /**
     * @return array
     * @throws PrestaShopException
     */
    public function getData() {
        $data = ['has_errors' => true, 'rss' => []];

        $circuitBreaker = $this->factory->create($this->blogSettings);
        $blogXMLResponse = $circuitBreaker->call(_PS_API_URL_ . '/rss/blog/blog-' . $this->isoCode . '.xml');

        if (empty($blogXMLResponse)) {
            return $data;
        }

        $rss = @simplexml_load_string($blogXMLResponse);
        if (!$rss) {
            return $data;
        }

        $articles_limit = self::NUM_ARTICLES;

        $shop_default_country_id = (int)Configuration::get('PS_COUNTRY_DEFAULT');
        $shop_default_iso_country = (string)Tools::strtoupper(Country::getIsoById($shop_default_country_id));
        $analytics_params = [
            'utm_source' => 'back-office',
            'utm_medium' => 'rss',
            'utm_campaign' => 'back-office-' . $shop_default_iso_country,
        ];

        foreach ($rss->channel->item as $item) {
            if ($articles_limit == 0
                || !Validate::isCleanHtml((string)$item->title)
                || !Validate::isCleanHtml((string)$item->description)
                || !isset($item->link, $item->title)) {
                break;
            }
            $analytics_params['utm_content'] = 'download';
            if (in_array($this->context->mode, array(Context::MODE_HOST, Context::MODE_HOST_CONTRIB))) {
                $analytics_params['utm_content'] = 'cloud';
            }
            $article_link = (string)$item->link . '?' . http_build_query($analytics_params);

            $url_query = parse_url($item->link, PHP_URL_QUERY);
            parse_str($url_query, $link_query_params);
            if ($link_query_params) {
                $full_url_params = array_merge($link_query_params, $analytics_params);
                $base_url = explode('?', (string)$item->link);
                $base_url = (string)$base_url[0];
                $article_link = $base_url . '?' . http_build_query($full_url_params);
            }

            $data['rss'][] = [
                'date' => Tools::displayDate(date('Y-m-d', strtotime((string)$item->pubDate))),
                'title' => (string)Tools::htmlentitiesUTF8($item->title),
                'short_desc' => Tools::truncateString(strip_tags((string)$item->description), 150),
                'link' => (string)$article_link,
            ];
            --$articles_limit;
        }
        $data['has_errors'] = false;
        return $data;
    }
}