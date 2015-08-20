<?php
namespace PrestaShop\PrestaShop\Core\Business\Controller\Admin;

use PrestaShop\PrestaShop\Core\Business\Controller\AdminController;
use PrestaShop\PrestaShop\Core\Foundation\Routing\Response;
use Symfony\Component\HttpFoundation\Request;
use PrestaShop\PrestaShop\Core\Business\Controller\AutoObjectInflaterTrait;
use PrestaShop\PrestaShop\Core\Business\Controller\AutoResponseFormatTrait;

class TestController extends AdminController
{
    use AutoObjectInflaterTrait;
    use AutoResponseFormatTrait;

    public function aAction(Request &$request, Response &$response)
    {
        $response->setContentData(array('A que coucou, de la part du Back, en HTML sans Layout!'));
        return self::RESPONSE_NUDE_HTML;
    }

    public function bAction(Request &$request, Response &$response)
    {
        $response->setContentData(array('b' => 'à bas (de la part du Back, en JSON)'));
        return self::RESPONSE_JSON;
    }

    public function cAction(Request &$request, Response &$response)
    {
        $response->setContentData(array('c' => 'cédille (de la part du Back, format auto, selon la requete HTTP)'));
        //return ??? // --> auto, with AutoResponseFormatTrait magic!
    }

    public function dAction(Request &$request, Response &$response)
    {
        echo 'D pité, on ejecte la sortie direct depuis le controller.';
        return self::RESPONSE_NONE; // declenche un exit(0) au lieu de send la response
    }
}
