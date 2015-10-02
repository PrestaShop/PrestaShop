<?php
namespace PrestaShop\PrestaShop\Core\Business\Controller\Front;

use PrestaShop\PrestaShop\Core\Business\Controller\FrontController;
use PrestaShop\PrestaShop\Core\Foundation\Routing\Response;
use Symfony\Component\HttpFoundation\Request;
use PrestaShop\PrestaShop\Core\Business\Controller\AutoResponseFormatTrait;

class TestController extends FrontController
{
    use AutoResponseFormatTrait;

    public function aAction(Request &$request, Response &$response)
    {
        $response->setContent('A que coucou, de la part du Front! En mode text brut');
        return self::RESPONSE_RAW_TEXT;
    }

    public function bAction(Request &$request, Response &$response)
    {
        $response->setContentData(array('b' => 'à bas (de la part du Front)'));
    }
}
