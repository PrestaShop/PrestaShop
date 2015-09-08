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
use PrestaShop\PrestaShop\Core\Foundation\Routing\Response;
use PrestaShop\PrestaShop\Core\Business\Routing\Router;

class Core_Foundation_Exception_Exception extends Exception
{
    public $reportData = null;
    private $randomInstantiationKey = null;

    public function __construct($message = null, $code = 0, \Exception $previous = null, $reportData = null)
    {
        parent::__construct($message, $code, $previous);
        $this->reportData = $reportData;
        $this->randomInstantiationKey = uniqid('psexc', true);
    }
    
    public function __toStringHtml()
    {
        $data =  '<b>"'.$this->message.'"</b><br/>';
        if ($afterMessageContent = $this->getAfterMessageContent()) {
            $data .= '<i>Alternative used data given: "'.(string)$afterMessageContent.'"</i><br/>';
        }
        $data .= '<ul><li><b>Exception type:</b> '.get_class($this).'</li>';
        $data .= '<li><b>Code #</b> '.$this->code.'</li>';
        $data .= '<li><b>Occurred at line</b> '.$this->line.' <b>in file</b> '.$this->file.'</li>';
        $data .= '<li><b>Time:</b> '.date('D, d M Y H:i:s').' / '.time().'</li>';
        $data .= '</ul><br/>';

        $data .= 'To send support information about this exception, please use this link:<br/>';
        $data .= '<a href="#">TODO (stratégie support à mettre en place, risque de flooding!)</a><br/><br/>';

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
    
    protected function getAfterMessageContent()
    {
        return null;
    }
}
