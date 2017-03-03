<?php return array (
  'imports' => 
  array (
    0 => 
    array (
      'resource' => 'set_parameters.php',
    ),
    1 => 
    array (
      'resource' => 'security.yml',
    ),
    2 => 
    array (
      'resource' => 'services.yml',
    ),
  ),
  'parameters' => 
  array (
    'AdapterSecurityAdminClass' => 'PrestaShop\\PrestaShop\\Adapter\\Security\\Admin',
    'translator.class' => 'PrestaShopBundle\\Translation\\Translator',
  ),
  'framework' => 
  array (
    'assets' => 
    array (
      'version' => '1.7.0',
    ),
    'secret' => '%secret%',
    'translator' => 
    array (
      'fallbacks' => 
      array (
        0 => 'default',
      ),
    ),
    'router' => 
    array (
      'resource' => '%kernel.root_dir%/config/routing.yml',
      'strict_requirements' => NULL,
    ),
    'form' => NULL,
    'csrf_protection' => NULL,
    'validation' => 
    array (
      'enable_annotations' => true,
    ),
    'templating' => 
    array (
      'engines' => 
      array (
        0 => 'twig',
      ),
    ),
    'default_locale' => '%locale%',
    'trusted_hosts' => NULL,
    'trusted_proxies' => NULL,
    'session' => 
    array (
      'handler_id' => NULL,
    ),
    'fragments' => NULL,
    'http_method_override' => true,
  ),
  'monolog' => 
  array (
    'handlers' => 
    array (
      'main' => 
      array (
        'type' => 'stream',
        'path' => '%kernel.logs_dir%/%kernel.environment%.log',
        'level' => 'notice',
      ),
      'legacy' => 
      array (
        'type' => 'service',
        'id' => 'prestashop.handler.log',
        'level' => 'warning',
        'channels' => 
        array (
          0 => 'app',
        ),
      ),
    ),
  ),
  'twig' => 
  array (
    'debug' => '%kernel.debug%',
    'strict_variables' => '%kernel.debug%',
    'form_themes' => 
    array (
      0 => 'PrestaShopBundle:Admin/TwigTemplateForm:bootstrap_3_horizontal_layout.html.twig',
    ),
  ),
  'doctrine' => 
  array (
    'dbal' => 
    array (
      'default_connection' => 'default',
      'connections' => 
      array (
        'default' => 
        array (
          'driver' => 'pdo_mysql',
          'host' => '%database_host%',
          'port' => '%database_port%',
          'dbname' => '%database_name%',
          'user' => '%database_user%',
          'password' => '%database_password%',
          'server_version' => 5.0999999999999996,
          'charset' => 'UTF8',
          'mapping_types' => 
          array (
            'enum' => 'string',
          ),
        ),
      ),
    ),
    'orm' => 
    array (
      'auto_generate_proxy_classes' => '%kernel.debug%',
      'naming_strategy' => 'prestashop.database.naming_strategy',
      'auto_mapping' => true,
      'dql' => 
      array (
        'string_functions' => 
        array (
          'regexp' => 'DoctrineExtensions\\Query\\Mysql\\Regexp',
        ),
      ),
    ),
  ),
  'swiftmailer' => 
  array (
    'transport' => '%mailer_transport%',
    'host' => '%mailer_host%',
    'username' => '%mailer_user%',
    'password' => '%mailer_password%',
    'spool' => 
    array (
      'type' => 'memory',
    ),
  ),
  'csa_guzzle' => 
  array (
    'profiler' => 
    array (
      'enabled' => '%kernel.debug%',
    ),
    'cache' => 
    array (
      'enabled' => true,
      'adapter' => 'guzzle.cache',
    ),
    'clients' => 
    array (
      'addons_api' => 
      array (
        'config' => 
        array (
          'base_url' => 'https://api-addons.prestashop.com',
          'defaults' => 
          array (
            'timeout' => '5.0',
          ),
          'headers' => 
          array (
            'Accept' => 'application/json',
          ),
        ),
      ),
    ),
  ),
  'prestashop' => 
  array (
    'addons' => 
    array (
      'api_client' => 
      array (
        'ttl' => 7200,
      ),
    ),
  ),
);
