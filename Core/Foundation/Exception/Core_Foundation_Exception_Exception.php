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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
use PrestaShop\PrestaShop\Core\Business\Routing\Router;
use PrestaShop\PrestaShop\Core\Foundation\Dispatcher\EventDispatcher;

/**
 * This is a common layer over Exception, to add basic features.
 *
 * (using dispatchers, with a formatted __toString() output, etc...)
 */
class Core_Foundation_Exception_Exception extends Exception
{
    /**
     * @var EventDispatcher
     */
    protected static $messageDispatcher = null;

    /**
     * Sets the EventDispatcher to allow subclasses to dispatch messages when they are instantiated.
     *
     * @param EventDispatcher $dispatcher
     */
    final public static function setMessageDispatcher(EventDispatcher $dispatcher)
    {
        self::$messageDispatcher = $dispatcher;
    }

    /**
     * A set of data (unknown format) to link to the Exception.
     * Used in __toString() to show Exception details.
     *
     * @var mixed
     */
    public $reportData = null;

    private $randomInstantiationKey = null;
    private $moduleToDeactivate = null;

    /**
     * Constructor.
     *
     * @param string $message
     * @param number $code
     * @param \Exception $previous
     * @param mixed $reportData
     * @param mixed $moduleToDeactivate
     */
    public function __construct($message = null, $code = 0, \Exception $previous = null, $reportData = null, $moduleToDeactivate = null)
    {
        parent::__construct($message, $code, $previous);
        $this->reportData = $reportData;
        $this->randomInstantiationKey = uniqid('psexc', true);
        $this->moduleToDeactivate = $moduleToDeactivate;
        $this->deactivateModule();
    }

    /**
     * Overrides default __toString() to add a lot of event data to the output.
     */
    public function __toString()
    {
        if (!func_num_args() || func_get_arg(0) == false) {
            return parent::__toString();
        }

        $data =  '<b>'.$this->message.'</b><br/>';
        if ($afterMessageContent = $this->getAfterMessageContent()) {
            $data .= '<i>Alternative used data given: "'.(string)$afterMessageContent.'"</i><br/>';
        }
        $data .= '<ul><li><b>Exception type:</b> '.get_class($this).'</li>';
        if ($this->moduleToDeactivate != null) {
            $data .= '<li><b>THIS MODULE HAS BEEN DEACTIVATED:</b> '.$this->moduleToDeactivate.'</li>';
        }
        $data .= '<li><b>Code #</b> '.$this->code.'</li>';
        $data .= '<li><b>Occurred at line</b> '.$this->line.' <b>in file</b> '.$this->file.'</li>';
        $data .= '<li><b>Time:</b> '.date('D, d M Y H:i:s').' / '.time().'</li>';
        $data .= '</ul><br/>';

        $data .= 'To send support information about this exception, please use this link:<br/>';
        $data .= '<a href="#">TODO (This link will allow to send report to support team directly, in a future release)</a><br/><br/>';

        if ($this->reportData != null && count($this->reportData) > 0) {
            $data .= '<a name="'.$this->randomInstantiationKey.'_reporting_block"></a><a href="#'.$this->randomInstantiationKey.'_reporting_block"
            onclick="$(this).next(\'.reporting_block\').toggle();">[+] <b>Reporting data</b></a>
            <span class="reporting_block" style="display: none;">: <br/><ul>';
            foreach ((array)$this->reportData as $reportKey => $reportItem) {
                $data .= '<li><b>'.$reportKey.'</b>: '.(string)$reportItem.'</li>';
            }
            $data .= '</ul></span><br/>';
        }

        $data .= '<a name="'.$this->randomInstantiationKey.'_technical_block"></a><a href="#'.$this->randomInstantiationKey.'_technical_block"
            onclick="$(this).next(\'.technical_block\').toggle();">[+] <b>Technical data</b></a>
            <span class="technical_block" style="display: none;">: <br/><ul>';
        try {
            $request = Router::getLastRouterRequestInstance();
            if ($request !== null) {
                $data .= '<li><b>Route from module tracking:</b> '.$request->attributes->get('_route_from_module').'</li>';
                $data .= '<li><b>Controller from module tracking:</b> '.$request->attributes->get('_controller_from_module').'</li>';
            }
        } catch (\Exception $e) {
        }
        $data .= '<li><b>File #</b> '.md5_file($this->file).'</li>';
        $data .= '<li><b>URL:</b> <a href="http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'">http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'</a></li>';
        $data .= '<li><b>Stack trace:</b> '.parent::getTraceAsString().'</li>';
        if ($e = $this->getPrevious()) {
            $data .= '<li><b>Previous Exception:</b> '.$e->__toString().'</li>';
        }
        $data .= '</ul></span><br/>';

        return $data;
    }

    /**
     * Does nothing in this class. Must be overriden to appears in the __toString() dumping.
     *
     * @return mixed
     */
    protected function getAfterMessageContent()
    {
        return null;
    }

    /**
     * TODO to code when modules will be able to register features into the new Core.
     * This will deactivate the module. The merchant will have to reactivate it manually.
     *
     * @return boolean
     */
    protected function deactivateModule()
    {
        if ($this->moduleToDeactivate != null) {
            // TODO
            return true;
        }
        return false;
    }
}
