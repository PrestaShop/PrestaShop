<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

/**
 * In charge of downloading the latest Prestashop Version.
 *
 * Most methods are copied from https://github.com/PrestaShop/autoupgrade/blob/master/classes/TaskRunner/Upgrade/Download.php
 * and https://github.com/PrestaShop/autoupgrade/blob/master/classes/Tools14.php
 */
class Download
{
    const PRESTASHOP_API_RELEASES_XML_FEED = 'https://api.prestashop.com/xml/channel.xml';
    const CACHED_FEED_FILENAME = 'XMLFeed';

    /**
     * @var BasicFileCache
     */
    private $cachingSystem;

    /**
     * @param BasicFileCache $cachingSystem optional FileCache
     */
    public function __construct(?BasicFileCache $cachingSystem = null)
    {
        if (null === $cachingSystem) {
            $cachingSystem = new BasicFileCache();
        }

        $this->cachingSystem = $cachingSystem;
    }

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

        if (!extension_loaded('openssl') && strpos($url, 'https://') !== false) {
            $url = str_replace('https', 'http', $url);
        }

        $stream_context = null;
        if (preg_match('/^https?:\/\//', $url)) {
            $stream_context = @stream_context_create([
                'http' => ['timeout' => $curl_timeout, 'header' => "User-Agent:MyAgent/1.0\r\n"],
            ]);
        }

        if (in_array(ini_get('allow_url_fopen'), ['On', 'on', '1']) || !preg_match('/^https?:\/\//', $url)) {
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

            return $content;
        }
        return false;
    }

    /**
     * @return VersionNumber
     */
    public function getLatestStableAvailableVersion()
    {
        $feed = $this->getFeed();

        $branch = $this->getLatestStableBranchObjectFromFeed($feed);
        $versionNumberAsString = (string) $branch->num;

        return VersionNumber::fromString($versionNumberAsString);
    }

    /**
     * @return string
     */
    public function getLatestStableAvailableVersionLink()
    {
        $feed = $this->getFeed();

        $branch = $this->getLatestStableBranchObjectFromFeed($feed);

        return (string) $branch->download->link;
    }

    public function clearFileCache()
    {
        $this->cachingSystem->delete(self::CACHED_FEED_FILENAME);
    }

    /**
     * @return SimpleXMLElement
     *
     * @throws Exception
     */
    private function getFeed()
    {
        if (false === $this->isXmlFeedStoredInCache()) {
            $feed = @file_get_contents(self::PRESTASHOP_API_RELEASES_XML_FEED);

            if (false === $feed) {
                throw new PrestashopCouldNotDownloadLatestVersionException('Could not fetch API releases xml feed');
            }

            $this->storeFeedIntoFileCache($feed);
        }

        $feed = $this->getXmlFeedFromCache();
        $xml = simplexml_load_string($feed);

        if (false === $xml) {
            throw new PrestashopCouldNotDownloadLatestVersionException('Could not parse API releases xml feed');
        }

        return $xml;
    }

    /**
     * @param SimpleXMLElement $feed
     *
     * @return SimpleXMLElement|null
     *
     * @throws PrestashopCouldNotDownloadLatestVersionException
     */
    private function getLatestStableBranchObjectFromFeed($feed)
    {
        foreach ($feed->channel as $channel) {
            $channelName = (string) $channel['name'];

            if ('stable' !== $channelName) {
                continue;
            }

            $maxStableVersion = null;
            $maxStableBranch = null;
            foreach ($channel->branch as $branch) {
                $versionNumberAsString = (string) $branch->num;
                $versionNumber = VersionNumber::fromString($versionNumberAsString);

                if (null === $maxStableVersion) {
                    $maxStableVersion = $versionNumber;
                    $maxStableBranch = $branch;
                } elseif (1 === $versionNumber->compare($maxStableVersion)) {
                    $maxStableVersion = $versionNumber;
                    $maxStableBranch = $branch;
                }
            }

            return $maxStableBranch;
        }

        throw new PrestashopCouldNotDownloadLatestVersionException(
            'Could not find latest stable version from API releases xml feed'
        );
    }

    /**
     * @return bool
     */
    private function isXmlFeedStoredInCache()
    {
        return $this->cachingSystem->isCached(self::CACHED_FEED_FILENAME);
    }

    /**
     * @return string
     *
     * @throws Exception
     */
    private function getXmlFeedFromCache()
    {
        return $this->cachingSystem->get(self::CACHED_FEED_FILENAME);
    }

    /**
     * @param string $xml
     *
     * @return bool
     *
     * @throws Exception
     */
    private function storeFeedIntoFileCache($xml)
    {
        return $this->cachingSystem->save($xml, self::CACHED_FEED_FILENAME);
    }
}
