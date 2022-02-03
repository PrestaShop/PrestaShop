<?php

namespace PrestaShopBundle\Bridge;

use \Cookie;
use \Country;
use \Language;
use \Link;
use PrestaShopBundle\Security\Admin\Employee;
use \Shop;

class ControllerConfiguration
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $controllerName;

    /**
     * @var string
     */
    public $controllerNameLegacy;

    /**
     * @var string
     */
    public $currentIndex;

    /**
     * @var string
     */
    public $positionIdentifier;

    /**
     * @var string
     */
    public $table;

    /**
     * @var string
     */
    public $token;

    /**
     * @var Employee
     */
    public $user;

    /**
     * @var array
     */
    public $metaTitle;

    /**
     * @var array
     */
    public $breadcrumbs = [];

    /**
     * @var bool
     */
    public $liteDisplay = false;

    /**
     * @var string
     * @Todo replace this by a route parameter
     */
    public $display = 'list';

    /**
     * @var bool
     */
    public $showPageHeaderToolbar = true;

    /**
     * @var string
     */
    public $pageHeaderToolbarTitle = '';

    /**
     * @var array List of available actions for each list row - default actions are view, edit, delete, duplicate
     */
    public $actionsAvailable = ['view', 'edit', 'duplicate', 'delete'];

    /**
     * @var array
     */
    public $actions = [];

    /**
     * @var array
     */
    public $bulkActions;

    /**
     * @var array
     */
    public $pageHeaderToolbarButton = [];

    /**
     * @var array
     */
    public $toolbarButton = [];

    /**
     * @var array
     */
    public $toolbarTitle = [];

    /**
     * @var array
     */
    public $filter = [];

    /**
     * @var array
     */
    public $fieldsList;

    /**
     * @var bool
     */
    public $displayHeader = true;

    /**
     * @var bool
     */
    public $displayHeaderJavascript = true;

    /**
     * @var bool
     */
    public $displayFooter = true;

    /**
     * @var bool
     */
    public $bootstrap = true;

    /**
     * @var array
     */
    public $cssFiles = [];

    /**
     * @var array
     */
    public $jsFiles = [];

    /**
     * @var string
     */
    public $folderTemplate;

    /**
     * @var array
     */
    public $errors = [];

    /**
     * @var array
     */
    public $warnings = [];

    /**
     * @var array
     */
    public $confirmations = [];

    /**
     * @var array
     */
    public $informations = [];

    /**
     * @var bool
     */
    public $json = false;

    /**
     * @var string
     */
    public $template = 'content.tpl';

    /**
     * @var array
     */
    public $templatesVars = [];

    /**
     * @var Link
     */
    public $link;

    /**
     * @var Language
     */
    public $language;

    /**
     * @var Cookie
     */
    public $cookie;

    /**
     * @var Shop
     */
    public $shop;

    /**
     * @var Country
     */
    public $country;
}
