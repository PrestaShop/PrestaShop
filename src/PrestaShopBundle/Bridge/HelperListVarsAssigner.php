<?php

namespace PrestaShopBundle\Bridge;

use \Configuration;

class HelperListVarsAssigner
{
    /**
     * @var BreadcrumbsAndTitleHydrator
     */
    private $breadcrumbsAndTitleHydrator;

    /**
     * @var ToolbarFlagsHydrator
     */
    private $toolbarFlagsHydrator;

    public function __construct(BreadcrumbsAndTitleHydrator $breadcrumbsAndTitleHydrator, ToolbarFlagsHydrator $toolbarFlagsHydrator)
    {
        $this->breadcrumbsAndTitleHydrator = $breadcrumbsAndTitleHydrator;
        $this->toolbarFlagsHydrator = $toolbarFlagsHydrator;
    }

    /**
     * This function sets various display options for helper list.
     *
    //* @param HelperList|HelperView|HelperOptions $helper
     */
    public function setHelperDisplay(
        ControllerConfiguration $controllerConfiguration,
        HelperListConfiguration $helperListConfiguration,
        $helper
    ) {
        if (empty($controllerConfiguration->breadcrumbs)) {
            $this->breadcrumbsAndTitleHydrator->hydrate($controllerConfiguration);
        }

        if (empty($controllerConfiguration->toolbarTitle)) {
            $this->toolbarFlagsHydrator->hydrate($controllerConfiguration);
        }
        //// tocheck
        //if ($this->object && $this->object->id) {
        //    $helper->id = $this->object->id;
        //}
        //
        //// @todo : move that in Helper
        /// Sortons ça d'ici et mettons le en dur => Pas pas mettre en dur car traduit depuis la BDD
        $helper->title = is_array($controllerConfiguration->toolbarTitle) ? implode(' ' . Configuration::get('PS_NAVIGATION_PIPE') . ' ', $controllerConfiguration->toolbarTitle) : $controllerConfiguration->toolbarTitle;
        $helper->toolbar_btn = $controllerConfiguration->toolbarButton;
        //$helper->show_toolbar = $this->show_toolbar;
        $helper->show_toolbar = true;
        //$helper->toolbar_scroll = $this->toolbar_scroll;
        //$helper->override_folder = $this->tpl_folder;
        $helper->actions = $controllerConfiguration->actions;
        //$helper->simple_header = $this->list_simple_header;
        $helper->bulk_actions = $controllerConfiguration->bulkActions;
        //todo handle this
        //$helper->currentIndex = $this->generateUrl('admin_features_index');
        $helper->currentIndex = '';
        //Useless for list
        //if (isset($helper->className)) {
        //    $helper->className = $this->className;
        //}
        $helper->table = $controllerConfiguration->table;
        if (isset($helper->name_controller)) {
            $helper->name_controller = $controllerConfiguration->controllerNameLegacy;
        }
        //Useless for list
        $helper->orderBy = $helperListConfiguration->orderBy;
        //Useless for list
        //$helper->orderWay = $this->_orderWay;
        $helper->listTotal = $helperListConfiguration->listTotal;
        //if (isset($helper->shopLink)) {
        //    $helper->shopLink = $this->shopLink;
        //}
        //$helper->shopLinkType = $this->shopLinkType;
        $helper->identifier = $helperListConfiguration->identifier;
        //$helper->token = $controllerConfiguration->token;
        //// @phpstan-ignore-next-line
        //$helper->languages = $this->_languages;
        //$helper->specificConfirmDelete = $this->specificConfirmDelete;
        //$helper->imageType = $this->imageType;
        //$helper->no_link = $this->list_no_link;
        //$helper->colorOnBackground = $this->colorOnBackground;
        //$helper->ajax_params = isset($this->ajax_params) ? $this->ajax_params : null;
        //// @phpstan-ignore-next-line
        //$helper->default_form_language = $this->default_form_language;
        //if (isset($helper->allow_employee_form_lang)) {
        //    $helper->allow_employee_form_lang = $this->allow_employee_form_lang;
        //}
        //if (isset($helper->multiple_fieldsets)) {
        //    $helper->multiple_fieldsets = $this->multiple_fieldsets;
        //}
        //$helper->row_hover = $this->row_hover;
        $helper->position_identifier = $controllerConfiguration->positionIdentifier;
        //if (isset($helper->position_group_identifier)) {
        //    $helper->position_group_identifier = $this->position_group_identifier;
        //}
        //// @phpstan-ignore-next-line
        $helper->controller_name = $controllerConfiguration->controllerNameLegacy;
        $helper->list_id = $helperListConfiguration->listId ?? $controllerConfiguration->table;
        $helper->bootstrap = $controllerConfiguration->bootstrap;
        //
        //// For each action, try to add the corresponding skip elements list
        //$helper->list_skip_actions = $this->list_skip_actions;
    }
}
