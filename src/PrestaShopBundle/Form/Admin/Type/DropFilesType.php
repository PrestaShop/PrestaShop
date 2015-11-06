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
namespace PrestaShopBundle\Form\Admin\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use PrestaShopBundle\Form\Admin\Validator\DropFile;

/**
 * This form class is responsible to create/manage a drag&drop form field
 */
class DropFilesType extends AbstractType
{
    private $label;
    private $dropzoneOptions;
    private $dropzonePostUrl;

    /**
     * Constructor
     *
     * @param string $label The field label
     * @param string $dropzonePostUrl The url to post files
     * @param array $dropzoneOptions The options to render/translate the dropzone JS lib (see : http://www.dropzonejs.com)
     */
    public function __construct($label = '', $dropzonePostUrl = '', $dropzoneOptions = null)
    {
        $this->label = $label;
        $this->dropzoneOptions = $dropzoneOptions;
        $this->dropzonePostUrl = $dropzonePostUrl;

        //create a new cache/tmp/uploads folder
        if (!is_dir(_PS_CACHE_DIR_.'tmp'.DIRECTORY_SEPARATOR.'upload')) {
            $oldUmask = umask(0000);
            mkdir(_PS_CACHE_DIR_.'tmp'.DIRECTORY_SEPARATOR.'upload', 0777, true);
            umask($oldUmask);
        }
    }

    /**
     * {@inheritdoc}
     *
     * Add the uploaded files datas to the view
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $arrayExistedFiles = array();
        if ($form->get('files')->getData()) {
            foreach ($form->get('files')->getData() as $file) {
                $arrayExistedFiles[] = json_decode($file);
            }
        }

        $view->vars['dropzoneOptions'] = json_encode($this->dropzoneOptions);
        $view->vars['dropzoneExsitedFiles'] = $arrayExistedFiles;
        $view->vars['dropzonePostUrl'] = $this->dropzonePostUrl;
    }

    /**
     * {@inheritdoc}
     *
     * Builds form
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('files', 'collection', array(
            'type' => 'hidden',
            'required' => false,
            'prototype' => false,
            'allow_add' => true,
            'allow_delete' => true,
            'delete_empty' => true,
            'label' => $this->label,
            'constraints' => array(
                new DropFile()
            )
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'drop_files';
    }
}
