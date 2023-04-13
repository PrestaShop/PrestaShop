<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShopBundle\Command;

use Address;
use Cart;
use CartRule;
use Customer;
use Db;
use Faker\Factory;
use Order;
use OrderState;
use Product;
use Shop;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * This command is used for appending the hook names in the configuration file.
 */
class ShopCreatorCommand extends Command
{
    /**
     * @var string
     */
    private $dbPrefix;

    public function __construct(string $dbPrefix)
    {
        parent::__construct();
        $this->dbPrefix = $dbPrefix;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('prestashop:shop-creator')
            ->addOption('orders', null, InputOption::VALUE_OPTIONAL, 'Number of orders to create', 10000)
            ->addOption('customers', null, InputOption::VALUE_OPTIONAL, 'Number of customers without order to create', 5000)
            ->addOption('carts', null, InputOption::VALUE_OPTIONAL, 'Number of carts to create', 50000)
            ->addOption('cart-rules', null, InputOption::VALUE_OPTIONAL, 'Number of cart rules to create', 10000)
            ->addOption('products', null, InputOption::VALUE_OPTIONAL, 'Number of products to create', 10000)
            ->addOption('shopId', null, InputOption::VALUE_OPTIONAL, 'The shop identifier', 1)
            ->addOption('shopGroupId', null, InputOption::VALUE_OPTIONAL, 'The shop group identifier', 1)
            ->addOption('languageId', null, InputOption::VALUE_OPTIONAL, 'The languageId identifier', 1)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        \Context::getContext()->currency = \Currency::getDefaultCurrency();

        $faker = Factory::create('fr_FR');

        $numberOfOrders = (int) $input->getOption('orders');
        $numberOfCustomerWithoutOrder = (int) $input->getOption('customers');
        $numberOfCarts = (int) $input->getOption('carts');
        $numberOfCartRules = (int) $input->getOption('cart-rules');
        $numberOfProducts = (int) $input->getOption('products');
        $idLang = (int) $input->getOption('languageId');
        $idshop = (int) $input->getOption('shopId');
        $idShopGroup = (int) $input->getOption('shopGroupId');

        $productIds = $this->getSimpleProducts($idLang);
        for ($i = 0; $i < $numberOfCustomerWithoutOrder; ++$i) {
            $customer = new Customer();
            $customer->firstname = $faker->firstName;
            $customer->lastname = $faker->lastName;
            $customer->email = $faker->email;
            $customer->passwd = '$2y$10$WzLnGz9j..JtTFcjfjoWr.8L/rw39NwovNRwPxf6yk/AYWcIj/1Au';
            $customer->add();
            $output->writeln('Customer without order ' . ($i + 1) . ' created successfully.');
        }
        for ($i = 0; $i < $numberOfCarts; ++$i) {
            $cart = new Cart();
            $cart->id_currency = 1; // Modifier cette valeur selon les devises disponibles dans votre boutique
            $cart->add();

            for ($j = 0; $j < $faker->numberBetween(1, 5); ++$j) {
                $randomProduct = $faker->randomElement($productIds);
                $cart->updateQty($faker->numberBetween(1, 3), $randomProduct['id_product']);
            }
            $output->writeln('Cart ' . ($i + 1) . ' created successfully.');
        }

        $output->writeln('End generation 1k carts without order');

        for ($i = 0; $i < $numberOfOrders; ++$i) {
            $customer = new Customer();
            $customer->firstname = $faker->firstName;
            $customer->lastname = $faker->lastName;
            $customer->email = $faker->email;
            $customer->passwd = '$2y$10$WzLnGz9j..JtTFcjfjoWr.8L/rw39NwovNRwPxf6yk/AYWcIj/1Au';
            $customer->add();

            $address = new Address();
            $address->id_customer = $customer->id;
            $address->alias = 'default';
            $address->firstname = $customer->firstname;
            $address->lastname = $customer->lastname;
            $address->address1 = $faker->streetAddress;
            $address->city = $faker->city;
            $address->postcode = $faker->postcode;
            $address->id_country = $faker->numberBetween(1, 10); // Modifier cette valeur selon les pays disponibles dans votre boutique
            $address->phone = $faker->phoneNumber;
            $address->dni = '1234567891012131';
            $address->add();

            $cart = new Cart();
            $cart->id_customer = $customer->id;
            $cart->id_address_delivery = $address->id;
            $cart->id_address_invoice = $address->id;
            $cart->id_currency = 1; // Modifier cette valeur selon les devises disponibles dans votre boutique
            $cart->add();

            for ($j = 0; $j < $faker->numberBetween(1, 5); ++$j) {
                $randomProduct = $faker->randomElement($productIds);
                $cart->updateQty($faker->numberBetween(1, 3), $randomProduct['id_product']);
            }

            $order = new Order();
            $order->id_shop_group = $idShopGroup;
            $order->id_shop = $idshop;
            $order->id_cart = $cart->id;
            $order->id_customer = $customer->id;
            $order->id_address_delivery = $address->id;
            $order->id_address_invoice = $address->id;
            $order->id_currency = $cart->id_currency;
            $order->id_carrier = 1;
            $order->id_lang = $cart->id_lang;
            $order->payment = 'Faker Payment';
            $order->module = 'fakerpayment';
            $order->total_paid = $cart->getOrderTotal(true, Cart::BOTH);
            $order->total_paid_real = $cart->getOrderTotal(true, Cart::BOTH);
            $order->total_paid_tax_incl = $cart->getOrderTotal(true, Cart::BOTH);
            $order->total_paid_tax_excl = $cart->getOrderTotal(false, Cart::BOTH);
            $order->total_products = $cart->getOrderTotal(false, Cart::ONLY_PRODUCTS);
            $order->total_products_wt = $cart->getOrderTotal(true, Cart::ONLY_PRODUCTS);
            $order->total_shipping = 0;
            $order->secure_key = md5('test');
            $order->carrier_tax_rate = 0;
            $order->conversion_rate = 1;

            $order->add();

            $orderStatus = new OrderState(3); // 3 est l'ID du statut de la commande "en cours de préparation"
            $order->setCurrentState($orderStatus->id);
            $order->save();

            $output->writeln('Order ' . ($i + 1) . ' created successfully.');
        }

        for ($i = 0; $i < $numberOfCartRules; ++$i) {
            $cartRule = new CartRule();
            $cartRule->name = [$idLang => 'Fake Cart Rule ' . ($i + 1)];
            $cartRule->description = 'Generated by the Faker library';
            $cartRule->code = strtoupper($faker->bothify('FAKE-CART-RULE-????'));
            $cartRule->quantity = $faker->numberBetween(1, 100);
            $cartRule->quantity_per_user = $faker->numberBetween(1, 10);
            $cartRule->priority = $faker->numberBetween(1, 10);
            $cartRule->partial_use = $faker->boolean(50);
            $cartRule->minimum_amount = $faker->randomFloat(2, 0, 500);
            $cartRule->minimum_amount_currency = 1; // Change this value according to the currencies available in your store
            $cartRule->reduction_percent = $faker->numberBetween(10, 90);
            $cartRule->free_shipping = $faker->boolean(50);
            $cartRule->active = true;

            // Set the date range for the cart rule
            $startDate = $faker->dateTimeBetween('-1 year', 'now');
            $endDate = $faker->dateTimeBetween($startDate, '+1 year');
            $cartRule->date_from = $startDate->format('Y-m-d H:i:s');
            $cartRule->date_to = $endDate->format('Y-m-d H:i:s');

            $cartRule->add();
            $output->writeln('Cart Rule ' . ($i + 1) . " created successfully.\n");
        }

        for ($i = 0; $i < $numberOfProducts; ++$i) {
            $product = new Product();
            $productName = 'Fake Product ' . ($i + 1);

            $product->name = [$idLang => $productName];
            $product->description = 'This is a fake product generated by the Faker library.';
            $product->description_short = 'Generated by the Faker library.';
            $product->price = $faker->randomFloat(2, 1, 1000);
            $product->reference = $faker->bothify('FAKE-PRODUCT-????');
            $product->active = true;

            $product->add();

            // Associate the product to categories (use your own category IDs)
            $categories = [2, 3, 4];
            $product->updateCategories($categories);

            echo 'Product ' . ($i + 1) . " created successfully.\n";
        }

        return 0;
    }

    /**
     * @param int $id_lang Language identifier
     *
     * @return array
     */
    private function getSimpleProducts($id_lang, bool $front = true)
    {
        $sql = 'SELECT p.`id_product`, pl.`name`
                FROM `' . $this->dbPrefix . 'product` p
                ' . Shop::addSqlAssociation('product', 'p') . '
                LEFT JOIN `' . $this->dbPrefix . 'product_lang` pl ON (p.`id_product` = pl.`id_product` ' . Shop::addSqlRestrictionOnLang('pl') . ')
                WHERE pl.`id_lang` = ' . (int) $id_lang . '
                ' . ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '') . '
                ORDER BY pl.`name`';

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }
}
