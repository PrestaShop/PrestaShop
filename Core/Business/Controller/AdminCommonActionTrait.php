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
namespace PrestaShop\PrestaShop\Core\Business\Controller;

use PrestaShop\PrestaShop\Core\Foundation\Routing\Response;
use Symfony\Component\HttpFoundation\Request;
use PrestaShop\PrestaShop\Core\Foundation\Form\FormFactory;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\File;
use PrestaShop\PrestaShop\Core\Foundation\Controller\BaseController;

/**
 * This Trait will add common action such as upload, delete (droping files on the interface like images), change status and more...
 */
trait AdminCommonActionTrait
{
    /**
     * Used by Dropfiles plugin to upload files asynchronously.
     *
     * @param Request $request
     * @param Response $response
     * @return string
     */
    public function uploadAction(Request &$request, Response &$response)
    {
        /*$formFactory = new FormFactory(null, array('csrf_protection' => false));
        $builder = $formFactory->create();*/

        $formFactory = new FormFactory();
        $builder = $formFactory->createBuilder('form', null, array('csrf_protection' => false));

        $constraints = array();

        if ($request->get('file_type') == 'image') {
            $constraints = array(new Image(array(
                    'maxSize' => '1024k',
                    'mimeTypes' => array(
                        'image/jpeg',
                        'image/jpg',
                        'image/png',
                        'image/gif'
                    )
                )));
        } elseif ($request->get('file_type') == 'file') {
            $constraints = array( new File(array(
                'maxSize' => '1024k'
            )));
        }

        $form = $builder
            ->add('file', 'file', array(
                'error_bubbling' => true,
                'constraints' => $constraints
            ))
            ->getForm();

        $form->handleRequest($request);

        if ($request->isMethod('POST')) {
            if ($form->isValid()) {
                $file = $form->getData()['file'];

                $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                $file->move(_PS_CACHE_DIR_.'tmp'.DIRECTORY_SEPARATOR.'upload', $fileName);

                $return_data = array(
                    'file_original_name' => $file->getClientOriginalName(),
                    'file_name_tmp' => $fileName,
                    'file_path_tmp' => _PS_CACHE_DIR_.'tmp'.DIRECTORY_SEPARATOR.'upload'.DIRECTORY_SEPARATOR.$fileName,
                    'file_url_tmp' => 'http://localhost'.__PS_BASE_URI__.'cache/tmp/upload/'.$fileName,
                    'file_type' => $file->getClientMimeType(),
                    'filesize' => filesize(_PS_CACHE_DIR_.'tmp'.DIRECTORY_SEPARATOR.'upload'.DIRECTORY_SEPARATOR.$fileName)
                );
            } else {
                $error_msg = array();
                foreach ($form->getErrors() as $key => $error) {
                    $error_msg[] = $error->getMessage();
                }
                $return_data = array('message' => implode(" ", $error_msg));
                $response->setStatusCode(403);
            }
        }

        $response->setContentData($return_data);

        return self::RESPONSE_JSON;
    }

    /**
     * This will allow you to retrieve an HTML code from the navigatorAction with a ready and linked navigator.
     *
     * To be able to use this navigator, the current route must have these standard parameters:
     * - offset
     * - limit
     * Both will be automatically manipulated by the navigator.
     * The navigator links (previous/next page...) will never tranfer POST and/or GET parameters
     * (only route parameters that are in the URL).
     *
     * The navigator will add a javascript dependency, and will add a $navigator variable in the response Data array.
     * So you just have to call this method and then use {$navigator} in your template.
     *
     * @param Request $request The original request to retrieve route parameters (to generate links)
     * @param Response $response The original response, to let the function add Javascript dependencies and the resulting navigator HTML part.
     * @param integer $totalCount The total count of elements to paginate (not the count of one page).
     */
    final protected function addNavigatorToResponse(Request &$request, Response &$response, $totalCount)
    {
        $navigatorParams = array_merge(
            $request->attributes->all(),
            array(
                '_total' => $totalCount,
            )
        );
        $navigator = $this->subcall('admin_tools_navigator', $navigatorParams, BaseController::RESPONSE_PARTIAL_VIEW);
        $response->addContentData('navigator', $navigator);
        $response->addJs(_PS_JS_DIR_.'Core/Admin/Navigator.js');

        return true; // success.
    }

    /**
     * This action is mainly used as a subcall from Admin page actions to generate a navigator.
     *
     * A row of links to navigates through paginated list of elements.
     * Please call it via $this->fetchNavigator() method (that will make the subcall with the right parameters).
     *
     * @param Request $request
     * @param Response $response
     */
    public function navigatorAction(Request &$request, Response &$response)
    {
        $response->setTemplate('Core/Controller/Admin/navigator.tpl');

        // base elements
        $total = $request->attributes->get('caller_parameters')['_total'];
        $offset = $request->attributes->get('caller_parameters')['offset'];
        $limit = $request->attributes->get('caller_parameters')['limit'];
        if ($limit <= 0) {
            $limit = 10;
        }
        $currentPage = floor($offset/$limit)+1;
        $pageCount = ceil($total/$limit);
        $from = $offset;
        $to = $offset+$limit-1;

        // urls from route
        $callerParameters = $request->attributes->get('caller_parameters', array());
        foreach ($callerParameters as $k => $v) {
            if (strpos($k, '_') === 0) {
                unset($callerParameters[$k]);
            }
        }
        $routeName = $request->attributes->get('caller_parameters')['_route'];

        $nextPageUrl = ($offset+$limit >= $total) ? false : $this->generateUrl($routeName, array_merge(
            $callerParameters,
            array(
                'offset' => min($total-1, $offset+$limit),
                'limit' => $limit
            )
        ));
        $previousPageUrl = ($offset == 0) ? false : $this->generateUrl($routeName, array_merge(
            $callerParameters,
            array(
                'offset' => max(0, $offset-$limit),
                'limit' => $limit
            )
        ));
        $firstPageUrl = ($offset == 0) ? false : $this->generateUrl($routeName, array_merge(
            $callerParameters,
            array(
                'offset' => 0,
                'limit' => $limit
            )
        ));
        $lastPageUrl = ($offset+$limit >= $total) ? false : $this->generateUrl($routeName, array_merge(
            $callerParameters,
            array(
                'offset' => ($pageCount-1)*$limit,
                'limit' => $limit
            )
        ));
        $changeLimitUrl = $this->generateUrl($routeName, array_merge(
            $callerParameters,
            array(
                'offset' => 0,
                'limit' => '_limit'
            )
        ));

        $response->setContentData(array(
            'total' => $total,
            'offset' => $offset,
            'limit' => $limit,
            'current_page' => $currentPage,
            'page_count' => $pageCount,
            'from' => min($from + 1, min(array($to+1, $total))),
            'to' => min(array($to+1, $total)),
            'next_url' => $nextPageUrl,
            'previous_url' => $previousPageUrl,
            'first_url' => $firstPageUrl,
            'last_url' => $lastPageUrl,
            'changeLimitUrl' => $changeLimitUrl
        ));
        return self::RESPONSE_PARTIAL_VIEW;
    }

    /**
     * This action return form errors for JS implementation
     *
     * Parse all errors mapped by id html field
     *
     * @param $form The form
     *
     * @return array Errors
     */
    protected function getFormErrorsForJS($form)
    {
        $errors = [];

        if (empty($form)) {
            return $errors;
        }

        foreach ($form->getErrors(true) as $error) {
            if (!$error->getCause()) {
                $form_id = 'bubbling_errors';
            } else {
                $form_id = str_replace(
                    ['.', 'children[', ']', '_data'],
                    ['_', '', '', ''],
                    $error->getCause()->getPropertyPath()
                );
            }
            $errors[$form_id][] = $error->getMessage();
        }
        return $errors;
    }
}
