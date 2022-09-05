<?php

namespace PrestaShop\PrestaShop\Core\Help;

use PrestaShop\PrestaShop\Core\Foundation\Version;

class Documentation
{
    /**
     * @var Version
     */
    private $version;

    /**
     * @var string
     */
    private $host;

    public function __construct(Version $version, string $host)
    {
        $this->version = $version;
        $this->host = $host;
    }

    public function generateLink(string $section, string $langIsoCode): string
    {
        return urlencode($this->host . $langIsoCode . '/doc/'
            . $section . '?version=' . $this->version->getSemVersion() . '&country=' . $langIsoCode);
    }
}
