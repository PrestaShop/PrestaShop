<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Exception;

use Exception;
use Throwable;

/**
 * Class TranslatableCoreException.
 */
class TranslatableCoreException extends CoreException
{
    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $domain;

    /**
     * @var array
     */
    private $parameters = [];

    /**
     * @param string $key
     * @param string $domain
     * @param array $parameters
     * @param int $code
     * @param Throwable|Exception|null $previous
     */
    public function __construct(
        $key,
        $domain,
        $parameters = [],
        $code = 0,
        $previous = null
    ) {
        parent::__construct($key, $code, $previous);
        $this->key = $key;
        $this->domain = $domain;
        $this->parameters = $parameters;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     *
     * @return $this
     */
    public function setKey($key)
    {
        $this->key = $key;
        $this->message = $key;

        return $this;
    }

    /**
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param string $domain
     *
     * @return $this
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param array $parameters
     *
     * @return $this
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'key' => $this->key,
            'domain' => $this->domain,
            'parameters' => $this->parameters,
        ];
    }
}
