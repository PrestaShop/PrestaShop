<?php
/**
 * 2007-2018 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

/**
 * @property Profile $object
 */
class AdminProfilesControllerCore extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->context = Context::getContext();
        $this->table = 'profile';
        $this->className = 'Profile';
        $this->multishop_context = Shop::CONTEXT_ALL;
        $this->lang = true;
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->addRowActionSkipList('delete', [1]);

        parent::__construct();

        $this->bulk_actions = [
            'delete' => [
                'text' => $this->trans('Delete selected', [], 'Admin.Actions'),
                'confirm' => $this->trans('Delete selected items?', [], 'Admin.Notifications.Warning'),
                'icon' => 'icon-trash',
            ],
        ];

        $this->fields_list = [
            'id_profile' => [
                        'title' => $this->trans('ID', [], 'Admin.Global'),
                        'align' => 'center',
                        'class' => 'fixed-width-xs',
                        ],
            'name' => ['title' => $this->trans('Name', [], 'Admin.Global')],
            ];

        $this->identifier = 'id_profile';

        $this->fields_form = [
            'legend' => [
                'title' => $this->trans('Profile', [], 'Admin.Advparameters.Feature'),
                'icon' => 'icon-group',
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->trans('Name', [], 'Admin.Global'),
                    'name' => 'name',
                    'required' => true,
                    'lang' => true,
                ],
            ],
            'submit' => [
                'title' => $this->trans('Save', [], 'Admin.Actions'),
            ],
        ];

        $list_profile = [];
        foreach (Profile::getProfiles($this->context->language->id) as $profil) {
            $list_profile[] = ['value' => $profil['id_profile'], 'name' => $profil['name']];
        }
    }

    public function postProcess()
    {
        /* PrestaShop demo mode */
        if (_PS_MODE_DEMO_) {
            $this->errors[] = $this->trans('This functionality has been disabled.', [], 'Admin.Notifications.Error');

            return;
        }
        /* PrestaShop demo mode*/

        if (isset($_GET['delete' . $this->table]) && $_GET[$this->identifier] == (int) (_PS_ADMIN_PROFILE_)) {
            $this->errors[] = $this->trans('For security reasons, you cannot delete the Administrator\'s profile.', [], 'Admin.Advparameters.Notification');
        } else {
            parent::postProcess();
        }
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_profile'] = [
                'href' => self::$currentIndex . '&addprofile&token=' . $this->token,
                'desc' => $this->trans('Add new profile', [], 'Admin.Advparameters.Feature'),
                'icon' => 'process-icon-new',
            ];
        }

        parent::initPageHeaderToolbar();
    }
}
