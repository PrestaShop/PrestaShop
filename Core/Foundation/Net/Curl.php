<?php
/**
 * 2007-2015 PrestaShop
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

namespace PrestaShop\PrestaShop\Core\Foundation\Net;

class Curl
{
    private $errorMessage;
    private $errorNumber;
    private $info;
    private $options;
    private $session;

    public function __construct()
    {
        if (!in_array('curl', get_loaded_extensions())) {
            throw new \RuntimeException('curl extension must be loaded');
        }
    }

    public function __destruct()
    {
        $this->close();
    }

    public function __get($name)
    {
        return $this->getInfo($name);
    }

    public function __isset($name)
    {
        return isset($this->info[$name]);
    }

    public function setErrorMessage($value)
    {
        $this->errorMessage = $value;
        return $this;
    }

    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    public function setErrorNumber($value)
    {
        $this->errorNumber = $value;
        return $this;
    }

    public function getErrorNumber()
    {
        return $this->errorNumber;
    }

    public function setInfo($value)
    {
        $this->info = $value;
        return $this;
    }

    public function getInfo($name = null)
    {
        if (isset($name) && isset($this->info[$name])) {
            return $this->info[$name];
        }

        return $this->info;
    }

    public function setOption($name, $value)
    {
        if (!isset($this->options)) {
            $this->options = array();
        }

        $this->options[$name] = $value;
        return $this;
    }

    public function getOption($name)
    {
        if (isset($this->options[$name])) {
            return $this->options[$name];
        }

        return null;
    }

    public function setOptions(array $value)
    {
        $this->options = $value;
        return $this;
    }

    public function getOptions()
    {
        if (!isset($this->options)) {
            $this->options = array();
        }

        return $this->options;
    }

    public function addOption($name, $value)
    {
        return $this->setOption($name, $value);
    }

    public function setUrl($value)
    {
        return $this->setOption(CURLOPT_URL, $value);
    }

    public function getUrl()
    {
        return $this->getOption(CURLOPT_URL);
    }

    public function close()
    {
        if ($this->isConnected()) {
            curl_close($this->session);
        }
    }

    public function connect()
    {
        if (!$this->isConnected()) {
            $this->session = curl_init();
        }

        return $this->session;
    }

    public function exec($url = null, $options = null)
    {
        $this->connect();

        if (isset($url)) {
            $this->setUrl($url);
        }

        if (isset($options)) {
            $this->setOptions($options);
        }

        if (isset($this->options)) {
            curl_setopt_array($this->session, $this->getOptions());
        }

        $content = curl_exec($this->session);
        $this->setErrorNumber(curl_errno($this->session));
        $this->setErrorMessage(curl_error($this->session));
        $this->setInfo(curl_getinfo($this->session));

        return $content;
    }

    public function isConnected()
    {
        return is_resource($this->session);
    }

    public function removeOption($name)
    {
        if (isset($this->options[$name])) {
            unset($this->options[$name]);
        }
    }
}
