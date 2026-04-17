<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
class VersionNumber
{
    /**
     * @var float
     */
    private $major;

    /**
     * @var int
     */
    private $minor;

    /**
     * @var int
     */
    private $patch;

    /**
     * @param float $major
     * @param int $minor
     * @param int $patch
     */
    public function __construct($major, $minor, $patch)
    {
        $this->major = $major;
        $this->minor = $minor;
        $this->patch = $patch;
    }

    /**
     * @param string $versionNumberAsString
     *
     * @return VersionNumber
     */
    public static function fromString($versionNumberAsString)
    {
        $regexp = '#^(\d+\.\d+|\d+)\.(\d+)\.(\d+)$#';
        $matches = [];

        $matchingResult = preg_match($regexp, $versionNumberAsString, $matches);

        if (1 !== $matchingResult) {
            throw new InvalidArgumentException(sprintf(
                'Failed to parse version number %s',
                $versionNumberAsString
            ));
        }

        return new static(
            $matches[1],
            $matches[2],
            $matches[3]
        );
    }

    /**
     * @return float
     */
    public function getMajor()
    {
        return $this->major;
    }

    /**
     * @return int
     */
    public function getMinor()
    {
        return $this->minor;
    }

    /**
     * @return int
     */
    public function getPatch()
    {
        return $this->patch;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('%g.%d.%d', $this->major, $this->minor, $this->patch);
    }

    /**
     * @param VersionNumber $otherNumber
     *
     * @return int 1 if this version number is higher, -1 if lower, 0 if equal
     */
    public function compare(VersionNumber $otherNumber)
    {
        if ($this->major > $otherNumber->getMajor()) {
            return 1;
        }
        if ($this->major < $otherNumber->getMajor()) {
            return -1;
        }

        if ($this->minor > $otherNumber->getMinor()) {
            return 1;
        }
        if ($this->minor < $otherNumber->getMinor()) {
            return -1;
        }

        if ($this->patch > $otherNumber->getPatch()) {
            return 1;
        }
        if ($this->patch < $otherNumber->getPatch()) {
            return -1;
        }

        return 0;
    }
}
