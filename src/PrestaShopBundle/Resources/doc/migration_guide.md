# Migration guide of Back Office page to Symfony 3

In order to migrate a legacy page, we need to migrate 3 parts of the application: the templates, the forms and the controllers which contains the business logic in PrestaShop.

## Templating with Twig

This is mostly the easy part. Legacy pages use Smarty when modern pages use Twig, theses templating engines are similar in so many ways.

For instance, this is a legacy template (all of them are located in `admin-dev/themes/default/template/controller`) folder:

```html
<span class="employee_avatar_small">
    <img class="img" alt="" src="{$employee_image}" />
</span>
{$employee_name}
```

and his (probable) migration to Twig:

```twig
<span class="employee_avatar_small">
    <img class="img" alt="{{ employee.name }}" src="{{ employee.image }}" />
</span>
{{ employee.name }}
```

Syntaxes are really similar, and we have ported every helper from Smarty to Twig:

| Smarty                                 | Twig                                                      |
|----------------------------------------|-----------------------------------------------------------|
| { l s='foo' d='domain' }               | {{ 'foo'\|trans({}, 'domain') }}                          |
| { hook h='hookName }                   | {{ renderhook('hookName') }}                              |
| {$link->getAdminLink('AdminAccess')}   | {{ getAdminLink('LegacyControllerName') }}                |

Macros/functions are specific to the modern pages to help with recurrent blocks:

* `form_label_tooltip(name, tooltip, placement)`: render a form label (by his name) with information in roll hover
* `check(variable)`: check if a variable is defined and not empty
* `tooltip(text, icon, position)`: render a tooltip with information in roll hover (doesn't render a label)
* `infotip(text)`, `warningtip(text)`: render information and warning tip (more like alert messages)
* `label_with_help(label, help)`: render a label with information in roll hover (render a label)

Finally, legacy templates use [Bootstrap 3](https://getbootstrap.com/docs/3.3/) when modern pages use the [PrestaShop UI Kit](http://build.prestashop.com/prestashop-ui-kit/) that relies on [Bootstrap 4](https://getbootstrap.com/docs/4.0/getting-started/introduction/), so you'll need to update some markup, especially CSS classes accordingly.

# Forms

## Legacy forms management

Forms are the biggest part of the migration. Before we have form helpers that mostly generate, validate and handle all the things when in Symfony every step (creation, validation and request handling) needs to be done by the developer.

For instance, this is code that you can find into a Legacy Controller:

```php
$this->fields_options = array(
    'general' => array(
        'title' => $this->trans('Logs by email', array(), 'Admin.Advparameters.Feature'),
        'icon' => 'icon-envelope',
        'fields' => array(
            'PS_LOGS_BY_EMAIL' => array(
                'title' => $this->trans('Minimum severity level', array(), 'Admin.Advparameters.Feature'),
                'hint' => $this->trans('Enter "5" if you do not want to receive any emails.', array(), 'Admin.Advparameters.Help'),
                'cast' => 'intval',
                'type' => 'text',
            ),
        ),
        'submit' => array('title' => $this->trans('Save', array(), 'Admin.Actions')),
    ),
);
```

This is how this configuration is rendered by the legacy controller (without anything to write in templates):

![Logs by email form](https://i.imgur.com/hAziI9Y.png)

The block is rendered and mapped to the controller url, the form is validated and mapped to the `PS_LOGS_BY_EMAIL` configuration key and automatically persisted in database, the label have a *hint* message in roll hover.

Let's see how we can do that in the modern pages.

## Modern form management

In modern pages and with Symfony, the form management is really decoupled from Controllers and you need to create your forms, to validate them, to map them to the current HTTP request and persist your data yourself. You also need to create your form templates (but we have a nice form theme already provided that helps you a lot with it).

### Form creation

Creation of forms using Symfony is already [documented](http://symfony.com/doc/current/forms.html) in their documentation.
You need to create your form types in `src/PrestaShopBundle/Form/Admin/{Page}/` folder, you can rely on existing forms to create your owns but at this moment there is nothing really specific to the PrestaShop integration.

Some Form types are subtypes to help you integrate the specific form inputs we use in the Back Office, you'll find them inside the *Types* folders:

* `ChoiceCategoryTreeType`
* `CustomMoneyType`
* `DatePickerType`
* `TextWithUnitType`
* ...

Most of the time, there are the Symfony integration of inputs defined in the PrestaShop UI Kit.
> Before create a new form input type, check first in this folder if the input exists.

Now a form is created and declared [as a service](http://symfony.com/doc/current/form/form_dependencies.html#define-your-form-as-a-service) you can use it inside your Controllers (we'll see it in the **Controllers** section of this guide).

### Form data providers

To manage existing data and save the data coming from user (submitting the form for instance), you need to create and register a Form Data provider.
You can rely on already existing implementations, or on the interface:

```php
interface FormDataProviderInterface
{
    /**
     * @return array the form data as an associative array
     */
    public function getData();

    /**
     * Persists form Data in Database and Filesystem.
     *
     * @param array $data
     * @return array $errors if data can't persisted an array of errors messages
     * @throws UndefinedOptionsException
     */
    public function setData(array $data);
}
```

The idea is to uncouple the data management from Controller, so populating current data and set new data will be done in theses implementations. Be careful, we are not persisting anything here.

### Form data handlers

Once you are able to manage data that comes from or should be sent by forms, you need a way to build your forms (they can be themselves composed of multiple forms) and to persist the data in filesystem or database. You need to create and register a Form data handler.
You can rely on already existing implementations, or on the interface:

```php
interface FormHandlerInterface
{
    /**
     * @return FormInterface
     */
    public function getForm();

    /**
     * Describe what need to be done on saving the form: mostly persists the data
     * using a form data provider, but it's also the right place to dispatch events/log something.
     *
     * @param array $data data retrieved from form that need to be persisted in database
     * @throws \Exception if the data can't be handled
     *
     * @return void
     */
    public function save(array $data);
}
```

> In some cases, you may want to rely on **$formDataProvider->setData()** directly, this behavior must be avoided.

### Form request handling in Controllers

In modern pages, Controllers have or should have only one responsability: handle the User request and return a response. This is why in modern pages, controllers should be as thin as possible and rely on specific classes (services) to manage the data. As always, you can rely on already existing implementations, like in the [PerformanceController](https://github.com/PrestaShop/PrestaShop/blob/develop/src/PrestaShopBundle/Controller/Admin/AdvancedParameters/PerformanceController.php).

This is how we manage a form inside a Controller:

```php
$form = $this->get('prestashop.adapter.performance.form_handler')->getForm();
$form->handleRequest($request);
/* ... some authorizations checks */
if ($form->isSubmitted()) {
    $data = $form->getData();
    $saveErrors = $this->get('prestashop.adapter.performance.form_handler')->save($data);
    if (0 === count($saveErrors)) {
        $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));
        return $this->redirectToRoute('admin_performance');
    }
    $this->flashErrors($saveErrors);
}
return $this->redirectToRoute('admin_performance');
}
```

So, basically three steps:

* Get information from User request and get form data;
* If form has been submitted, validate the form;
* If form is valid, save it. Else, return form errors and redirect.

> Every form in modern controllers must be handled this way, and the controller code should be kept minimalist but easier to read and to be understood.

### Render the form view, Twig templating

The rendering of forms in Twig is already [described](https://symfony.com/doc/current/form/rendering.html) in Symfony documentation. We use our own [Form theme](https://github.com/PrestaShop/PrestaShop/blob/develop/src/PrestaShopBundle/Resources/views/Admin/TwigTemplateForm/prestashop_ui_kit.html.twig) that contains specific input and markup for PrestaShop UI Kit, you can see it as a customized version of Bootstrap 4 form theme of Symfony 3, though we don't rely on it directly right now.

To sum up how it works, the controller send an instance of `FormView` to Twig and Twig have form helpers to render the right markups for every types of fields (because each Form Type have an associated markup described in the Form theme):

```twig
    {{ form_start(logsByEmailForm) }}
    <div class="col-md-12">
      <div class="col">
        <div class="card">
          <h3 class="card-header">
            <i class="material-icons">business_center</i> {{ 'Logs by email'|trans }}
          </h3>
          <div class="card-block">
            <div class="card-text">
              <div class="form-group row">
              {{ ps.label_with_help(('Minimum severity level'|trans), ('Enter "5" if you do not want to receive any emails.'|trans({}, 'Admin.Advparameters.Feature')), 'col-sm-2') }}
                <div class="col-sm-8">
                  {{ form_errors(logsByEmailForm.severity_level) }}
                  {{ form_widget(logsByEmailForm.severity_level) }}
                </div>
              </div>
            </div>
          </div>
          <div class="card-footer">
            <button class="btn btn-primary">{{ 'Save'|trans({}, 'Admin.Actions') }}</button>
          </div>
        </div>
      </div>
    </div>
    {{ form_end(logsByEmailForm) }}
```
All theses helpers are documented and help you to generate an HTML form from your `FormView` object, with the right markup to be rendered by the PrestaShop UI Kit. As for now, a lot of forms have already been migrated and rendered so you can rely and improve existing implementations.

Every templates from modern pages can be found inside `src/PrestaShopBundle/Resources/views/Admin` folder. Be careful, the organization of this templates [is about to change](https://github.com/PrestaShop/PrestaShop/pull/8489) soon (in 1.7.4) so try to keep, maintain or improve the organization.

Basically, we try to order template by page and domains, keep in mind each part of template can be overriden by PrestaShop developers using modules so use templates and Twig blocks wisely to make their job easy.

## Controller/Routing

### Modern/Symfony Controllers

> As always, you'll find all documentation you may need in Symfony documentation about [Controllers](https://symfony.com/doc/current/controller.html) and [Routing](https://symfony.com/doc/current/routing.html).

For every page we have to migrate we need to create one or more Controller: if you think a Legacy Controller need to be splitted into multiple controllers (good sign: differents urls locations), it's the right time to do it.

Every controller is created into `src/PrestaShopBundle/Controller/Admin` namespace. Since 1.7.3, we try to re-organize how theses controllers are created and we try to follow the menu from Back Office. For instance, if you are migrating a page located into "Advanced Parameters" section, put it into `src/PrestaShop/Controller/Admin/Configure/AdvancedParameters`. 
Same applies to **Improve** and **Sell** sections.

This is what we want to have in the end:

```
Controller/
└── Admin
    ├── Configure
    │   ├── AdvancedParameters
    │   └── ShopParameters
    ├── Improve
    │   ├── Design
    │   ├── International
    │   ├── Modules
    │   ├── Payment
    │   └── Shipping
    └── Sell
        ├── Catalog
        ├── Customers
        ├── CustomerService
        ├── Orders
        └── Stats
```

> Note: as Controllers are not available for override and can be regarded as internal classes, we don't consider moving a Controller in another namespace as a break of compatibility.

Symfony Controllers should be thin by default and have only one responsability: get the HTTP Request from user and return an HTTP Response. This means that every business logic should be done outside from Controller in dedicated classes:

* Form management
* Database access
* Validation
* etc...

You can take a look at [PerformanceController](https://github.com/PrestaShop/PrestaShop/blob/develop/src/PrestaShopBundle/Controller/Admin/AdvancedParameters/PerformanceController.php) for a good implementation, but at [ProductController](https://github.com/PrestaShop/PrestaShop/blob/develop/src/PrestaShopBundle/Controller/Admin/ProductController.php) for something you should avoid at all costs.

Once the Controller is created, it should contains "Actions". Actions are methods of Controllers (also called Controllers sometimes) mapped to a route, and with the responsability of returning a Response. You may avoid to create another functions, this probably means you should extract this code into external classes.

Regarding the rendering of a Response, there is some data specific to PrestaShop (in Back Office) that we must set to every action:

| Attribute                   |  Type                          |  Description                                            |
|-----------------------------|--------------------------------|---------------------------------------------------------|
| `layoutHeaderToolbarBtn`    | [['href', 'des','icon'], ...]  | Set buttons in toolbar on top of the page               |
| `layoutTitle`               | string                         | Main title of the page                                  |
| `requireAddonsSearch`       | boolean                        | If *true*, display addons recommendations button        |
| `requireBulkActions`        | boolean                        | If *true*, display bulk actions button                  |
| `showContentHeader`         | boolean                        | If *true*, display the page header                      |
| `enableSidebar`             | boolean                        | If *true*, display a sidebar                            |
| `help_link`                 | string                         | Set the url of "Help" button                            |
| `requireFilterStatus`       | boolean                        | ??? (Specific to Modules page?)                         |
| `level`                     | integer                        | Level of authorization for actions (Specific to modules)|

#### Helpers

Some helpers are specific to PrestaShop to help you manage the security and the dispatching of legacy hooks, all of them are directly available in Controllers that extends `FrameworkBundleAdminController`.

* `isDemoModeEnabled()`: some actions should not be allowed in Demonstration Mode
* `getDemoErrorMessage()`: returns a specific error message
* `addFlash(type, msg)`: accepts "success|error" and a message that will be display after redirection of the page
* `flashErrors([msgs])`: if you need to "flash" a collection of errors
* `dispatchHook(hookName, [params])`: some legacy hooks need to be dispatched to preserve backward compatibility
* `authorizationLevel(controllerName)`: check if you are allowed - as connected user - to do the related actions
* `langToLocale($lang)`: get the locale from a PrestaShop lang
* `trans(key, domain, [params])`: translate a string

### Routing in PrestaShop

In order to map an Action to an url, we need to register a route and update a legacy class called `Link`.
Routes are declared in `src/PrestaShopBundle/Resources/config/admin` folder, using a `routing_{domain}.yml` file and imported in `routing_admin.yml` file.

Nothing special here except that you *must* declare a property called `_legacy_controller` with the old name of controller you are migrating in order to make the class `Link` aware of it: this class is reponsible of generating urls in the legacy parts of PrestaShop.

Let's see what we have done when we have migrated the "System Information" page inside the "Configure >Advanced Parameters" section:

```yaml
admin_system_information:
    path: system_information
    methods: [GET]
    defaults:
        _controller: 'PrestaShopBundle\Controller\Admin\AdvancedParameters\SystemInformationController::indexAction'
        _legacy_controller: AdminInformation
```

> We have decided to use YAML for services declaration and routing, don't use annotations please!

And now the update of `Link` class:

```php
// classes/Link.php, in getAdminLink()
case 'AdminInformation':
                $sfRoute = array_key_exists('route', $sfRouteParams) ? $sfRouteParams['route'] : 'admin_system_information';

                return $sfRouter->generate($sfRoute, $sfRouteParams, UrlGeneratorInterface::ABSOLUTE_URL);
```

And now, every link to "System Information" page in legacy parts will point to the new url.

> Be careful, some urls are hardcoded in legacy! Make a search using an IDE like PHPStorm and use the Link class when needed in Controllers, "{$url->link->getAdminLink()}" in smarty or "{{ getAdminLink() }}" in Twig.

## Deletions

Now everything is migrated, refactored, extracted to specific classes and works like a charm, it's time to remove the migrated parts:
* delete the old controller.
* delete the old templates (delete `admin-dev/themes/default/template/controller/{name}` folder.

> NEVER call the legacy controller inside the new controller, it's a no go, no matter the reason!