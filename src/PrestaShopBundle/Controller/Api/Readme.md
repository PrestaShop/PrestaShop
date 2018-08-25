# PrestaShop API

## How to create a new route

### 1/ Declare your route
You must declare your route in one of the files in:
`src/PrestaShopBundle/Resources/config/routing/api/path/to/file.yml`.

If the file corresponding to your context does not exist, you can create it by taking care of declaring your new route file in:
`src/PrestaShopBundle/Resources/config/routing/api.yml`

Example, you want a route showing you the list of the warehouses available: in `src/PrestaShopBundle/Resources/config/routing/api/warehouse.yml`, add (if it does not exist)
```yml
_api_warehouse:
    resource: "api/warehouse.yml"
```

Then, create a file named `src/PrestaShopBundle/Resources/config/routing/api/warehouse.yml` and add your route:

```yml
api_warehouse_list_warehouses:
    path: /warehouses
    methods: [GET]
    defaults:
        _controller: prestashop.core.api.warehouse.controller:listWarehousesAction

```

### 2/ Create your controller
1) API controllers are in the folder: `src/PrestaShopBundle/Controller/Api/xxxController.php`.
In our example, create `src/PrestaShopBundle/Controller/Api/WarehouseController.php` if it does not exist.

2) Register your controller in the `services.yml` located in: `src/PrestaShopBundle/Resources/config/services.yml` like other API controllers (search `# Api - Controllers`).
 You need to register with the same `id` you put on your routing_xxx.yml _(here, `prestashop.core.api.warehouse.controller`)_.

3) Extend your controller with `ApiController`, then you should be able to use the Symfony container in your controller.

4) All your functions must return a `JsonResponse`.

5) Please, be simple, small controllers _(using Services if you need)_.

### 3/ Create Entities, Repositories and Services! (Optional)
Please, do not use Legacy PrestaShop classes, create your own Service related to your context. _(Here, Warehouse for example)_. Like your controllers, register them in the `services.yml`.
Your controller must be really simple, for the same warehouse example, we can imagine something like this:

```php
public function listWarehousesAction()
{
    $warehouses = $this->warehouseRepository->getWarehouse($tree = true);
    return new JsonResponse($warehouses, 200);
}
```

And put your logic into the Repository. If the logic is more complicated or not related to the entity, use services.

### 4/ JSON return nomenclature
We have 2 cases:
1) Simple list of data, return something like:
```php
$result = array(
    'data' => array(
        array(
            'id' => 1,
            'name' => 'Example 1',
        ),
        array(
            'id' => 2,
            'name' => 'Example 2',
        )
    )
);
```

2) A recursive data (for example, a tree), you must have a `tree` and `children` keys, return something like:
```php
$result = array(
    'data' => array(
        'tree' => array(
            'children' => array(
                array(
                    'id' => 1,
                    'name' => 'Example 1',
                    'children' => array(
                        array(
                            'id' => 11,
                            'name' => 'Children 1.1',
                        ),
                        array(
                            'id' => 12,
                            'name' => 'Children 1.2',
                        )
                    )
                ),
                array(
                    'id' => 2,
                    'name' => 'Example 2',
                    'children' => array(
                        array(
                            'id' => 21,
                            'name' => 'Children 2.1',
                        ),
                        array(
                            'id' => 22,
                            'name' => 'Children 2.2',
                        )
                    )
                )
            )
        )
    )
);
```
Moreover, we can send information about request in a array `info` like that:
```php
$result = array(
    'info' => array(
        'current_url' => 'http://yoururl.com/api/...'
        'next_url' => 'http://yoururl.com/api/...?page_index=3'
        'previous_url' => 'http://yoururl.com/api/...?page_index=1'
        'page_index' => 1,
        'page_size' => 100,
    )
    'data' => array() // with your data like above
);
```
_Be careful, some routes do not have pagination parameters because sometimes it is not relevant._
