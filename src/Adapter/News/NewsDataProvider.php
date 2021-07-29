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

namespace PrestaShop\PrestaShop\Adapter\News;

use Context;
use PrestaShop\CircuitBreaker\Contract\CircuitBreakerInterface;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\Country\CountryDataProvider;
use PrestaShop\PrestaShop\Adapter\Tools;
use PrestaShop\PrestaShop\Adapter\Validate;
use stdClass;

/**
 * Provide the news from https://www.prestashop.com/blog/
 */
class NewsDataProvider
{
    public const NUM_ARTICLES = 2;

    public const CLOSED_ALLOWED_FAILURES = 3;
    public const CLOSED_TIMEOUT_SECONDS = 3;

    public const OPEN_ALLOWED_FAILURES = 3;
    public const OPEN_TIMEOUT_SECONDS = 3;
    public const OPEN_THRESHOLD_SECONDS = 86400; // 24 hours

    /**
     * @var CircuitBreakerInterface
     */
    private $circuitBreaker;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var int
     */
    private $contextMode;

    /**
     * @var CountryDataProvider
     */
    private $countryDataProvider;

    /**
     * @var Tools
     */
    private $tools;

    /**
     * @var Validate
     */
    private $validate;

    /**
     * NewsDataProvider constructor.
     *
     * @param CircuitBreakerInterface $circuitBreaker
     * @param CountryDataProvider $countryDataProvider
     * @param Tools $tools
     * @param Configuration $configuration
     * @param Validate $validate
     * @param int $contextMode
     */
    public function __construct(
        CircuitBreakerInterface $circuitBreaker,
        CountryDataProvider $countryDataProvider,
        Tools $tools,
        Configuration $configuration,
        Validate $validate,
        $contextMode
    ) {
        $this->circuitBreaker = $circuitBreaker;
        $this->configuration = $configuration;
        $this->contextMode = $contextMode;
        $this->countryDataProvider = $countryDataProvider;
        $this->tools = $tools;
        $this->validate = $validate;
    }

    /**
     * @param string $isoCode
     *
     * @return array
     *
     * @throws \PrestaShopException
     */
    public function getData($isoCode)
    {
        $data = ['has_errors' => true, 'rss' => []];
        $apiUrl = $this->configuration->get('_PS_API_URL_');

        $blogXMLResponse = $this->circuitBreaker->call($apiUrl . '/rss/blog/blog-' . $isoCode . '.xml');

        if (empty($blogXMLResponse)) {
            $data['has_errors'] = false;

            return $data;
        }

        $rss = @simplexml_load_string($blogXMLResponse);
        if (!$rss) {
            return $data;
        }

        $articles_limit = self::NUM_ARTICLES;

        $shop_default_country_id = (int) $this->configuration->get('PS_COUNTRY_DEFAULT');
        $shop_default_iso_country = mb_strtoupper($this->countryDataProvider->getIsoCodebyId($shop_default_country_id), 'utf-8');
        $analytics_params = [
            'utm_source' => 'back-office',
            'utm_medium' => 'rss',
            'utm_campaign' => 'back-office-' . $shop_default_iso_country,
        ];

        /** @var stdClass $item */
        foreach ($rss->channel->item as $item) {
            if ($articles_limit == 0) {
                break;
            }
            if (!$this->validate->isCleanHtml((string) $item->title)
                || !$this->validate->isCleanHtml((string) $item->description)
                || empty($item->link)
                || empty($item->title)) {
                continue;
            }
            $analytics_params['utm_content'] = 'download';
            if (in_array($this->contextMode, [Context::MODE_HOST, Context::MODE_HOST_CONTRIB])) {
                $analytics_params['utm_content'] = 'cloud';
            }

            $url_query = parse_url($item->link, PHP_URL_QUERY);
            parse_str($url_query, $link_query_params);
            $full_url_params = array_merge($link_query_params, $analytics_params);
            $base_url = explode('?', (string) $item->link);
            $base_url = (string) $base_url[0];
            $article_link = $base_url . '?' . http_build_query($full_url_params);
            $date = strtotime($item->pubDate);
            $data['rss'][] = [
                'date' => $this->tools->displayDate(date('Y-m-d H:i:s', $date), null, false),
                'title' => htmlentities($item->title, ENT_QUOTES, 'utf-8'),
                'short_desc' => $this->tools->truncateString(strip_tags((string) $item->description), 150),
                'link' => (string) $article_link,
            ];
            --$articles_limit;
        }
        $data['has_errors'] = false;

        return $data;
    }
}
