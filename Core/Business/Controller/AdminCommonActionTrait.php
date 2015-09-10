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
use PrestaShop\PrestaShop\Form\FormFactory;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\File;

/**
 * This Trait will add common action such as uplad, delete, change status and more...
 */
trait AdminCommonActionTrait
{
    public function uploadAction(Request &$request, Response &$response)
    {
        $formFactory = new FormFactory(false);
        $builder = $formFactory->create();
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
}
