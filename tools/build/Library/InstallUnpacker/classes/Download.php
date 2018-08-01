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

class PrestashopCouldNotDownloadLatestVersionException extends \Exception
{
}

/**
 * In charge of downloading the latest Prestashop Version
 *
 * Most methods are copied from https://github.com/PrestaShop/autoupgrade/blob/master/classes/TaskRunner/Upgrade/Download.php
 * and https://github.com/PrestaShop/autoupgrade/blob/master/classes/Tools14.php
 */
class Download
{
    // @todo: what happens for PS 1.8+ ?
    const ADDONS_API_RELEASES_XML_FEED = 'https://api.prestashop.com/xml/channel17.xml';

    /**
     * @var string
     */
    private $xmlFeedStoredInCache;

    /**
     * @param string $source
     * @param string $destination
     *
     * @return bool|int
     */
    public static function copy($source, $destination)
    {
        return @file_put_contents($destination, self::fileGetContents($source));
    }

    /**
     * @param string $url
     *
     * @return bool|mixed|string
     */
    public static function fileGetContents($url)
    {
        $curl_timeout = 60;

        if (!extension_loaded('openssl') and strpos('https://', $url) === true) {
            $url = str_replace('https', 'http', $url);
        }

        $stream_context = null;
        if (preg_match('/^https?:\/\//', $url)) {
            $stream_context = @stream_context_create(array('http' => array('timeout' => $curl_timeout, 'header' => "User-Agent:MyAgent/1.0\r\n")));
        }

        if (in_array(ini_get('allow_url_fopen'), array('On', 'on', '1')) || !preg_match('/^https?:\/\//', $url)) {
            $var = @file_get_contents($url, false, $stream_context);

            if ($var) {
                return $var;
            }
        } elseif (function_exists('curl_init')) {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($curl, CURLOPT_TIMEOUT, $curl_timeout);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            $opts = stream_context_get_options($stream_context);
            
            if (isset($opts['http']['method']) && strtolower($opts['http']['method']) == 'post') {
                curl_setopt($curl, CURLOPT_POST, true);
                if (isset($opts['http']['content'])) {
                    parse_str($opts['http']['content'], $datas);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $datas);
                }
            }
            $content = curl_exec($curl);
            curl_close($curl);

            return $content;
        } else {
            return false;
        }
    }

    /**
     * @return string
     *
     * @throws \RuntimeException
     */
    public function getLatestStableAvailableVersion()
    {
        $feed = $this->getFeed();

        foreach ($feed->channel as $channel) {

            $channelName = (string)$channel['name'];
            if ('stable' === $channelName) {
                return (string)$channel->branch->num;
            }
        }

        throw new PrestashopCouldNotDownloadLatestVersionException('Could not find latest stable version from API releases xml feed');
    }

    /**
     * @return string
     *
     * @throws Exception
     */
    public function getLatestStableAvailableVersionLink()
    {
        $feed = $this->getFeed();

        foreach ($feed->channel as $channel) {

            $channelName = (string)$channel['name'];
            if ('stable' === $channelName) {
                return (string)$channel->branch->download->link;
            }
        }

        throw new PrestashopCouldNotDownloadLatestVersionException('Could not find latest stable version from API releases xml feed');
    }

    /**
     * @return SimpleXMLElement
     *
     * @throws Exception
     */
    private function getFeed()
    {
        if (null === $this->xmlFeedStoredInCache) {

            $feed = @file_get_contents(self::ADDONS_API_RELEASES_XML_FEED);

            if (false === $feed) {
                throw new PrestashopCouldNotDownloadLatestVersionException('Could not fetch API releases xml feed');
            }

            $xml = simplexml_load_string($feed);

            if (false === $xml) {
                throw new PrestashopCouldNotDownloadLatestVersionException('Could not read API releases xml feed');
            }

            $this->xmlFeedStoredInCache = $xml;
        }

        return $this->xmlFeedStoredInCache;
    }
}
