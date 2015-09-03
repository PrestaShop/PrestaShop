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

namespace PrestaShop\PrestaShop\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\File;

class DropFilesType extends AbstractType
{
    private $multiple;
    private $constraints;
    private $label;

    public function __construct($constraints = array(), $multiple = true, $label = false)
    {
        $this->constraints = $constraints;
        if (empty($this->constraints)) {
            $this->constraints = array(
                new Image(array(
                    'maxSize' => '1024k',
                    'minWidth' => 100,
                    'minHeight' => 100,
                    'mimeTypes' => array(
                        'image/jpeg',
                        'image/jpg',
                        'image/png',
                        'image/gif'
                    )
                )),
                new File(array(
                    'maxSize' => '1024k',
                    'mimeTypes' => array(
                        'text/plain'
                    )
                ))
            );
        }

        $this->multiple = $multiple;
        $this->label = $label;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('files', 'file', array(
            'label'     => $this->label,
            'multiple' => $this->multiple,
            'constraints' => $this->constraints,
            'attr' => array(
                'multiple' => $this->multiple ? 'multiple' : ''
            )
        ));
    }

    public function getName()
    {
        return 'drop_attachments';
    }
}
