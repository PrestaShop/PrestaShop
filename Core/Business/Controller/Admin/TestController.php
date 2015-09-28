<?php
namespace PrestaShop\PrestaShop\Core\Business\Controller\Admin;

use PrestaShop\PrestaShop\Core\Business\Controller\AdminController;
use PrestaShop\PrestaShop\Core\Foundation\Routing\Response;
use Symfony\Component\HttpFoundation\Request;
use PrestaShop\PrestaShop\Core\Business\Controller\AutoObjectInflaterTrait;
use PrestaShop\PrestaShop\Core\Business\Controller\AutoResponseFormatTrait;
use PrestaShop\PrestaShop\Core\Business\Controller\SfControllerResolverTrait;
use PrestaShop\PrestaShop\Core\Business\Context;
use PrestaShop\PrestaShop\Core\Foundation\Form\FormFactory;
use Symfony\Component\Validator\Constraints as Assert;
use PrestaShop\PrestaShop\Core\Foundation\Form\Validator\ContainsAlphanumeric;
use PrestaShop\PrestaShop\Core\Foundation\Form\Type\TestType;
use PrestaShop\PrestaShop\Core\Foundation\Form\Type\TranslateType;
use PrestaShop\PrestaShop\Core\Foundation\Form\Type\DropFilesType;

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
        $engine->view->display('layout.tpl');

        //to override template, if not : lookup by Controller/Action.(tpl|html.twig)
        $response->setTemplate('test.tpl');
        */

        $formFactory = new FormFactory();
        $builder = $formFactory->createBuilder();

        //create form from Class Form Type
        //$form = $formFactory->create(new \PrestaShop\PrestaShop\Core\Foundation\Form\Type\TestType());

        $simpleSubForm = $builder->create('author', 'form')
            ->add('name', 'text')
            ->add('email', 'text');

        $defaultData = array(
            'firstName' => 'tata',
            'lastName' => 'toto@sdfds.fr'
        );

        $form = $builder
            ->setAction('')
            ->add('firstName', 'text', array(
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array('min' => 4)),
                    new ContainsAlphanumeric(),
                ),
            ))
            ->add('lastName', 'text', array(
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Email(),
                    new Assert\Length(array('min' => 4)),
                ),
            ))
            ->add('gender', 'choice', array(
                'choices' => array('m' => 'Male', 'f' => 'Female'),
            ))
            ->add('newsletter', 'checkbox', array(
                'required' => false,
            ))
            ->add('imageAttachment', 'file', array(
                'required' => false,
                'constraints' => array(
                    new Assert\Image(array(
                        'maxSize' => '1024k',
                        'minWidth' => 100,
                        'minHeight' => 100,
                        'mimeTypes' => array(
                            'image/jpeg',
                            'image/jpg',
                            'image/png',
                            'image/gif'
                        )
                    ))
                )
            ))
            ->add('fileAttachment', 'file', array(
                'required' => false,
                'constraints' => array(
                    new Assert\File(array(
                        'maxSize' => '1024k',
                        'mimeTypes' => array(
                            'text/plain'
                        )
                    ))
                )
            ))
            ->add($simpleSubForm)
            ->add('testSimpleCollection', 'collection', array(
                'type' => new TestType(),
                'prototype' => true,
                'allow_add' => true,
                'allow_delete' => true))
            ->setData($defaultData)
            ->getForm();

        $form->handleRequest($request);

        /*foreach($form->getErrors(true) as $e){
            var_dump($e);die;
        }*/

        if ($form->isValid()) {
            $data = $form->getData();

            if (!empty($data['imageAttachment'])) {
                $file = $data['imageAttachment'];
                $file->move(_PS_UPLOAD_DIR_, md5(uniqid()) . '.' . $file->guessExtension());
            }

            if (!empty($data['fileAttachment'])) {
                $file = $data['fileAttachment'];
                $file->move(_PS_UPLOAD_DIR_, md5(uniqid()) . '_' . $file->getClientOriginalName());
            }

            //process droped files

            print_r($data);
            die;
            //do what you want, redirect...
        }

        $response->setEngineName('twig');
        $response->addContentData('form', $form->createView());

        $response->setLegacyControllerName('AdminCustomers');
        $response->setHeaderToolbarBtn(array('add' => array('href' => 'sdfsdfdsf', 'desc' => 'sdffdsfd', 'icon' => 'process-icon-new')));
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
