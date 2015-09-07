<?php
namespace PrestaShop\PrestaShop\Core\Business\Controller\Admin;

use PrestaShop\PrestaShop\Core\Business\Controller\AdminController;
use PrestaShop\PrestaShop\Core\Foundation\Routing\Response;
use Symfony\Component\HttpFoundation\Request;
use PrestaShop\PrestaShop\Core\Business\Controller\AutoObjectInflaterTrait;
use PrestaShop\PrestaShop\Core\Business\Controller\AutoResponseFormatTrait;
use PrestaShop\PrestaShop\Core\Foundation\Controller\SfControllerResolverTrait;
use PrestaShop\PrestaShop\Core\Business\Context;
use PrestaShop\PrestaShop\Core\Business\Routing\AdminRouter;

class TestController extends AdminController
{
    use AutoObjectInflaterTrait; // auto inflate objects if pattern found in the route format.
    use AutoResponseFormatTrait; // try to auto fill template engine parameters according to the current action.
    use SfControllerResolverTrait; // dependency injection in sf way.

    public function aAction(Request &$request, Response &$response)
    {

        /*
        VIEW EXAMPLE

        $engine = new \PrestaShop\PrestaShop\Core\Foundation\View\ViewFactory('twig');
        echo $engine->view->render('test.html.twig', array(
            'content' => 'toto',
            'aa' => '11'
        ));

        $engine->view->set('content', 'toto');
        $engine->view->display('test.html.twig', array('aa' => '22'));

        $engine->view->set('content', 'toto');
        echo $engine->view->fetch('test.html.twig', array('aa' => '33'));

        $engine = new \PrestaShop\PrestaShop\Core\Foundation\View\ViewFactory();
        $engine->view->set('content', 'toto');
        $engine->view->display('layout.tpl');*/

        $response->setLegacyControllerName('AdminAccess');
        //$response->setTemplate('test.tpl'); //to override template, if not : lookup by Controller/Action.(tpl|html.twig)
        $response->setContentData(array('content' => 'toto'));
    }

    public function bAction(Request &$request, Response &$response)
    {
        $response->addContentData('b', 'à bas (de la part du Back, en JSON)');
        return self::RESPONSE_JSON;
    }

    public function cAction(Request &$request, Response &$response, \Order $order)
    {
        $response->addContentData('c', 'cédille (de la part du Back, format auto, selon la requete HTTP)');
        //return ??? // --> auto, with AutoResponseFormatTrait magic!
    }

    public function dAction(Request &$request, Response &$response, Context $context)
    {
        echo 'D pité, on ejecte la sortie direct depuis le controller.';
        var_dump($this->generateUrl('admin_route2', array(
            'id_order' => 2,
            'toto' => 'titi'
        )));
        //$this->getRouter()->redirect('/admin-dev/index.php/a');
        return self::RESPONSE_NONE; // declenche un exit(0) au lieu de send la response
    }
    
    public function listAction(Request &$request, Response &$response, Context $context, $mykey)
    {
        var_dump($mykey);
        return self::RESPONSE_NUDE_HTML;
    }
}
