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

namespace PrestaShopBundle\Form\Admin\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints\File;

/**
 * DropFileValidator
 *
 * This class validate uploaded files from DropFilesType form type
 */
class DropFileValidator extends ConstraintValidator
{
    /**
     * Validate the new created image constraint
     *
     * @param array $files The files to test
     * @param Constraint $constraint
     */
    public function validate($files, Constraint $constraint)
    {
        $validator = Validation::createValidator();
        $constraint = array(new File(array(
            'maxSize' => '1024k',
            'mimeTypes' => array(
                'image/jpeg',
                'image/jpg',
                'image/png',
                'image/gif'
            )
        )));

        if (is_array($files) && !empty($files)) {
            foreach ($files as $file) {
                $file = json_decode($file);
                $uploadedFile = new UploadedFile($file->file_path_tmp, $file->file_original_name, null, null, null, true);

                $violations = $validator->validateValue($uploadedFile, $constraint);
                if (count($violations)>0) {
                    foreach ($violations as $violation) {
                        $this->context->addViolation($violation->getMessage());
                    }
                    break;
                }
            }
        }
    }
}
