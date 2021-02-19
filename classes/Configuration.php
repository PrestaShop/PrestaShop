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

/**
 * Class ConfigurationCore.
 */
class ConfigurationCore extends ObjectModel
{
    /** General */
    public const SSL_ENABLED = 'PS_SSL_ENABLED';
    public const SSL_ENABLED_EVERYWHERE = 'PS_SSL_ENABLED_EVERYWHERE';
    public const TOKEN_ENABLE = 'PS_TOKEN_ENABLE';
    public const ALLOW_HTML_IFRAME = 'PS_ALLOW_HTML_IFRAME';
    public const USE_HTMLPURIFIER = 'PS_USE_HTMLPURIFIER';
    public const PRICE_ROUND_MODE = 'PS_PRICE_ROUND_MODE';
    public const ROUND_TYPE = 'PS_ROUND_TYPE';
    public const DISPLAY_SUPPLIERS = 'PS_DISPLAY_SUPPLIERS';
    public const DISPLAY_MANUFACTURERS = 'PS_DISPLAY_MANUFACTURERS';
    public const DISPLAY_BEST_SELLERS = 'PS_DISPLAY_BEST_SELLERS';
    public const MULTISHOP_FEATURE_ACTIVE = 'PS_MULTISHOP_FEATURE_ACTIVE';
    public const SHOP_ACTIVITY = 'PS_SHOP_ACTIVITY';

    public const SHOP_ENABLE = 'PS_SHOP_ENABLE';
    public const MAINTENANCE_IP = 'PS_MAINTENANCE_IP';
    public const MAINTENANCE_TEXT = 'PS_MAINTENANCE_TEXT';

    /** Order Preferences */
    public const FINAL_SUMMARY_ENABLED = 'PS_FINAL_SUMMARY_ENABLED';
    public const GUEST_CHECKOUT_ENABLED = 'PS_GUEST_CHECKOUT_ENABLED';
    public const DISALLOW_HISTORY_REORDERING = 'PS_DISALLOW_HISTORY_REORDERING';
    public const PURCHASE_MINIMUM = 'PS_PURCHASE_MINIMUM';
    public const ORDER_RECALCULATE_SHIPPING = 'PS_ORDER_RECALCULATE_SHIPPING';
    public const ALLOW_MULTISHIPPING = 'PS_ALLOW_MULTISHIPPING';
    public const SHIP_WHEN_AVAILABLE = 'PS_SHIP_WHEN_AVAILABLE';
    public const CONDITIONS = 'PS_CONDITIONS';
    public const CONDITIONS_CMS_ID = 'PS_CONDITIONS_CMS_ID';

    public const  GIFT_WRAPPING = 'PS_GIFT_WRAPPING';
    public const  GIFT_WRAPPING_PRICE = 'PS_GIFT_WRAPPING_PRICE';
    public const  GIFT_WRAPPING_TAX_RULES_GROUP = 'PS_GIFT_WRAPPING_TAX_RULES_GROUP';
    public const  RECYCLABLE_PACK = 'PS_RECYCLABLE_PACK';

    /** Product Preferences */
    public const CATALOG_MODE = 'PS_CATALOG_MODE';
    public const CATALOG_MODE_WITH_PRICES = 'PS_CATALOG_MODE_WITH_PRICES';
    public const NB_DAYS_NEW_PRODUCT = 'PS_NB_DAYS_NEW_PRODUCT';
    public const PRODUCT_SHORT_DESC_LIMIT = 'PS_PRODUCT_SHORT_DESC_LIMIT';
    public const QTY_DISCOUNT_ON_COMBINATION = 'PS_QTY_DISCOUNT_ON_COMBINATION';
    public const FORCE_FRIENDLY_PRODUCT = 'PS_FORCE_FRIENDLY_PRODUCT';
    public const PRODUCT_ACTIVATION_DEFAULT = 'PS_PRODUCT_ACTIVATION_DEFAULT';

    public const DISPLAY_QTIES = 'PS_DISPLAY_QTIES';
    public const LAST_QTIES = 'PS_LAST_QTIES';
    public const DISP_UNAVAILABLE_ATTR = 'PS_DISP_UNAVAILABLE_ATTR';
    public const ATTRIBUTE_CATEGORY_DISPLAY = 'PS_ATTRIBUTE_CATEGORY_DISPLAY';
    public const ATTRIBUTE_ANCHOR_SEPARATOR = 'PS_ATTRIBUTE_ANCHOR_SEPARATOR';
    public const DISPLAY_DISCOUNT_PRICE = 'PS_DISPLAY_DISCOUNT_PRICE';

    public const ORDER_OUT_OF_STOCK = 'PS_ORDER_OUT_OF_STOCK';
    public const STOCK_MANAGEMENT = 'PS_STOCK_MANAGEMENT';
    public const LABEL_IN_STOCK_PRODUCTS = 'PS_LABEL_IN_STOCK_PRODUCTS';
    public const LABEL_OOS_PRODUCTS_BOA = 'PS_LABEL_OOS_PRODUCTS_BOA';
    public const LABEL_OOS_PRODUCTS_BOD = 'PS_LABEL_OOS_PRODUCTS_BOD';
    public const LABEL_DELIVERY_TIME_AVAILABLE = 'PS_LABEL_DELIVERY_TIME_AVAILABLE';
    public const LABEL_DELIVERY_TIME_OOSBOA = 'PS_LABEL_DELIVERY_TIME_OOSBOA';
    public const PACK_STOCK_TYPE = 'PS_PACK_STOCK_TYPE';

    public const PRODUCTS_PER_PAGE = 'PS_PRODUCTS_PER_PAGE';
    public const PRODUCTS_ORDER_BY = 'PS_PRODUCTS_ORDER_BY';
    public const PRODUCTS_ORDER_WAY = 'PS_PRODUCTS_ORDER_WAY';

    /** Customer preferences */
    public const CART_FOLLOWING = 'PS_CART_FOLLOWING';
    public const CUSTOMER_CREATION_EMAIL = 'PS_CUSTOMER_CREATION_EMAIL';
    public const PASSWD_TIME_FRONT = 'PS_PASSWD_TIME_FRONT';
    public const B2B_ENABLE = 'PS_B2B_ENABLE';
    public const CUSTOMER_BIRTHDATE = 'PS_CUSTOMER_BIRTHDATE';
    public const CUSTOMER_OPTIN = 'PS_CUSTOMER_OPTIN';

    public const UNIDENTIFIED_GROUP =  'PS_UNIDENTIFIED_GROUP';
    public const GUEST_GROUP =  'PS_GUEST_GROUP';
    public const CUSTOMER_GROUP =  'PS_CUSTOMER_GROUP';

    public const SHOP_NAME = 'PS_SHOP_NAME';
    public const SHOP_EMAIL = 'PS_SHOP_EMAIL';
    public const SHOP_DETAILS = 'PS_SHOP_DETAILS';
    public const SHOP_ADDR1 = 'PS_SHOP_ADDR1';
    public const SHOP_ADDR2 = 'PS_SHOP_ADDR2';
    public const SHOP_CODE = 'PS_SHOP_CODE';
    public const SHOP_CITY = 'PS_SHOP_CITY';
    public const SHOP_COUNTRY_ID = 'PS_SHOP_COUNTRY_ID';
    public const SHOP_STATE_ID = 'PS_SHOP_STATE_ID';
    public const SHOP_PHONE = 'PS_SHOP_PHONE';
    public const SHOP_FAX = 'PS_SHOP_FAX';

    /** Traffic & Seo */
    public const PRODUCT_ATTRIBUTES_IN_TITLE =  'PS_PRODUCT_ATTRIBUTES_IN_TITLE';

    public const REWRITING_SETTINGS = 'PS_REWRITING_SETTINGS';
    public const ALLOW_ACCENTED_CHARS_URL = 'PS_ALLOW_ACCENTED_CHARS_URL';
    public const CANONICAL_REDIRECT = 'PS_CANONICAL_REDIRECT';
    public const HTACCESS_DISABLE_MULTIVIEWS = 'PS_HTACCESS_DISABLE_MULTIVIEWS';
    public const HTACCESS_DISABLE_MODSEC = 'PS_HTACCESS_DISABLE_MODSEC';

    public const SHOP_DOMAIN = 'PS_SHOP_DOMAIN';
    public const SHOP_DOMAIN_SSL = 'PS_SHOP_DOMAIN_SSL';

    public const ROUTE_CATEGORY_RULE =  'PS_ROUTE_category_rule';
    public const ROUTE_SUPPLIER_RULE =  'PS_ROUTE_supplier_rule';
    public const ROUTE_MANUFACTURER_RULE =  'PS_ROUTE_manufacturer_rule';
    public const ROUTE_CMS_RULE =  'PS_ROUTE_cms_rule';
    public const ROUTE_CMS_CATEGORY_RULE =  'PS_ROUTE_cms_category_rule';
    public const ROUTE_MODULE =  'PS_ROUTE_module';
    public const ROUTE_PRODUCT_RULE =  'PS_ROUTE_product_rule';
    public const ROUTE_LAYERED_RULE =  'PS_ROUTE_layered_rule';

    /** Search */
    public const SEARCH_INDEXATION = 'PS_SEARCH_INDEXATION';
    public const SEARCH_START = 'PS_SEARCH_START';
    public const SEARCH_END = 'PS_SEARCH_END';
    public const SEARCH_FUZZY = 'PS_SEARCH_FUZZY';
    public const SEARCH_FUZZY_MAX_LOOP = 'PS_SEARCH_FUZZY_MAX_LOOP';
    public const SEARCH_MAX_WORD_LENGTH = 'PS_SEARCH_MAX_WORD_LENGTH';
    public const SEARCH_MINWORDLEN = 'PS_SEARCH_MINWORDLEN';
    public const SEARCH_BLACKLIST = 'PS_SEARCH_BLACKLIST';

    public const SEARCH_WEIGHT_PNAME =  'PS_SEARCH_WEIGHT_PNAME';
    public const SEARCH_WEIGHT_REF =  'PS_SEARCH_WEIGHT_REF';
    public const SEARCH_WEIGHT_SHORTDESC =  'PS_SEARCH_WEIGHT_SHORTDESC';
    public const SEARCH_WEIGHT_DESC =  'PS_SEARCH_WEIGHT_DESC';
    public const SEARCH_WEIGHT_CNAME =  'PS_SEARCH_WEIGHT_CNAME';
    public const SEARCH_WEIGHT_MNAME =  'PS_SEARCH_WEIGHT_MNAME';
    public const SEARCH_WEIGHT_TAG =  'PS_SEARCH_WEIGHT_TAG';
    public const SEARCH_WEIGHT_ATTRIBUTE =  'PS_SEARCH_WEIGHT_ATTRIBUTE';
    public const SEARCH_WEIGHT_FEATURE =  'PS_SEARCH_WEIGHT_FEATURE';

    /** Performance */
    public const SMARTY_FORCE_COMPILE = 'PS_SMARTY_FORCE_COMPILE';
    public const SMARTY_CACHE = 'PS_SMARTY_CACHE';
    public const SMARTY_LOCAL = 'PS_SMARTY_LOCAL';
    public const SMARTY_CACHING_TYPE = 'PS_SMARTY_CACHING_TYPE';
    public const SMARTY_CLEAR_CACHE = 'PS_SMARTY_CLEAR_CACHE';
    public const SMARTY_CONSOLE = 'PS_SMARTY_CONSOLE';
    public const SMARTY_CONSOLE_KEY = 'PS_SMARTY_CONSOLE_KEY';

    public const DISABLE_NON_NATIVE_MODULE = 'PS_DISABLE_NON_NATIVE_MODULE';
    public const DISABLE_OVERRIDES = 'PS_DISABLE_OVERRIDES';

    public const COMBINATION_FEATURE_ACTIVE = 'PS_COMBINATION_FEATURE_ACTIVE';
    public const FEATURE_FEATURE_ACTIVE = 'PS_FEATURE_FEATURE_ACTIVE';
    public const GROUP_FEATURE_ACTIVE = 'PS_GROUP_FEATURE_ACTIVE';

    public const CSS_THEME_CACHE = 'PS_CSS_THEME_CACHE';
    public const JS_THEME_CACHE = 'PS_JS_THEME_CACHE';
    public const HTACCESS_CACHE_CONTROL = 'PS_HTACCESS_CACHE_CONTROL';

    public const MEDIA_SERVER_1 = 'PS_MEDIA_SERVER_1';
    public const MEDIA_SERVER_2 = 'PS_MEDIA_SERVER_2';
    public const MEDIA_SERVER_3 = 'PS_MEDIA_SERVER_3';

    /** Administration */
    public const PRESTASTORE_LIVE = 'PRESTASTORE_LIVE';

    public const COOKIE_CHECKIP = 'PS_COOKIE_CHECKIP';
    public const COOKIE_LIFETIME_FO = 'PS_COOKIE_LIFETIME_FO';
    public const COOKIE_LIFETIME_BO = 'PS_COOKIE_LIFETIME_BO';
    public const COOKIE_SAMESITE = 'PS_COOKIE_SAMESITE';

    public const ATTACHMENT_MAXIMUM_SIZE = 'PS_ATTACHMENT_MAXIMUM_SIZE';
    public const LIMIT_UPLOAD_FILE_VALUE = 'PS_LIMIT_UPLOAD_FILE_VALUE';
    public const LIMIT_UPLOAD_IMAGE_VALUE = 'PS_LIMIT_UPLOAD_IMAGE_VALUE';

    public const SHOW_NEW_ORDERS = 'PS_SHOW_NEW_ORDERS';
    public const SHOW_NEW_CUSTOMERS = 'PS_SHOW_NEW_CUSTOMERS';
    public const SHOW_NEW_MESSAGES = 'PS_SHOW_NEW_MESSAGES';

    /** Email */
    public const MAIL_EMAIL_MESSAGE = 'PS_MAIL_EMAIL_MESSAGE';
    public const MAIL_METHOD = 'PS_MAIL_METHOD';
    public const MAIL_TYPE = 'PS_MAIL_TYPE';
    public const LOG_EMAILS = 'PS_LOG_EMAILS';
    public const MAIL_DOMAIN = 'PS_MAIL_DOMAIN';
    public const MAIL_SERVER = 'PS_MAIL_SERVER';
    public const MAIL_USER = 'PS_MAIL_USER';
    public const MAIL_SMTP_ENCRYPTION = 'PS_MAIL_SMTP_ENCRYPTION';
    public const MAIL_SMTP_PORT = 'PS_MAIL_SMTP_PORT';
    public const MAIL_PASSWD = 'PS_MAIL_PASSWD';

    /** Team */
    public const PASSWD_TIME_BACK = 'PS_PASSWD_TIME_BACK';
    public const BO_ALLOW_EMPLOYEE_FORM_LANG = 'PS_BO_ALLOW_EMPLOYEE_FORM_LANG';

    /** Database */
    public const ENCODING_FILE_MANAGER_SQL = 'PS_ENCODING_FILE_MANAGER_SQL';
    public const BACKUP_ALL = 'PS_BACKUP_ALL';
    public const BACKUP_DROP_TABLE = 'PS_BACKUP_DROP_TABLE';

    /** Logs */
    public const LOGS_BY_EMAIL = 'PS_LOGS_BY_EMAIL';
    public const LOGS_EMAIL_RECEIVERS = 'PS_LOGS_EMAIL_RECEIVERS';

    /** Webservice */
    public const WEBSERVICE = 'PS_WEBSERVICE';
    public const WEBSERVICE_CGI_HOST = 'PS_WEBSERVICE_CGI_HOST';

    /** Multistore */
    public const SHOP_DEFAULT =  'PS_SHOP_DEFAULT';

    /** Localization */
    public const LANG_DEFAULT = 'PS_LANG_DEFAULT';
    public const DETECT_LANG = 'PS_DETECT_LANG';
    public const COUNTRY_DEFAULT = 'PS_COUNTRY_DEFAULT';
    public const DETECT_COUNTRY = 'PS_DETECT_COUNTRY';
    public const CURRENCY_DEFAULT = 'PS_CURRENCY_DEFAULT';
    public const TIMEZONE = 'PS_TIMEZONE';

    public const WEIGHT_UNIT = 'PS_WEIGHT_UNIT';
    public const DISTANCE_UNIT = 'PS_DISTANCE_UNIT';
    public const VOLUME_UNIT = 'PS_VOLUME_UNIT';
    public const DIMENSION_UNIT = 'PS_DIMENSION_UNIT';

    public const RESTRICT_DELIVERED_COUNTRIES =  'PS_RESTRICT_DELIVERED_COUNTRIES';

    public const LOCALE_LANGUAGE = 'PS_LOCALE_LANGUAGE';
    public const LOCALE_COUNTRY = 'PS_LOCALE_COUNTRY';

    public const GEOLOCATION_BEHAVIOR = 'PS_GEOLOCATION_BEHAVIOR';
    public const GEOLOCATION_NA_BEHAVIOR = 'PS_GEOLOCATION_NA_BEHAVIOR';
    public const ALLOWED_COUNTRIES = 'PS_ALLOWED_COUNTRIES';

    public const GEOLOCATION_ENABLED = 'PS_GEOLOCATION_ENABLED';
    public const GEOLOCATION_WHITELIST = 'PS_GEOLOCATION_WHITELIST';

    /** Taxes */
    public const TAX = 'PS_TAX';
    public const TAX_DISPLAY = 'PS_TAX_DISPLAY';
    public const TAX_ADDRESS_TYPE = 'PS_TAX_ADDRESS_TYPE';
    public const USE_ECOTAX = 'PS_USE_ECOTAX';
    public const ECOTAX_TAX_RULES_GROUP_ID = 'PS_ECOTAX_TAX_RULES_GROUP_ID';

    /** Shipping */
    public const CARRIER_DEFAULT = 'PS_CARRIER_DEFAULT';
    public const CARRIER_DEFAULT_SORT = 'PS_CARRIER_DEFAULT_SORT';
    public const CARRIER_DEFAULT_ORDER = 'PS_CARRIER_DEFAULT_ORDER';

    public const SHIPPING_HANDLING = 'PS_SHIPPING_HANDLING';
    public const SHIPPING_FREE_PRICE = 'PS_SHIPPING_FREE_PRICE';
    public const SHIPPING_FREE_WEIGHT = 'PS_SHIPPING_FREE_WEIGHT';

    /** Images */
    public const IMAGE_QUALITY = 'PS_IMAGE_QUALITY';
    public const JPEG_QUALITY = 'PS_JPEG_QUALITY';
    public const PNG_QUALITY = 'PS_PNG_QUALITY';
    public const IMAGE_GENERATION_METHOD = 'PS_IMAGE_GENERATION_METHOD';
    public const PRODUCT_PICTURE_MAX_SIZE = 'PS_PRODUCT_PICTURE_MAX_SIZE';
    public const PRODUCT_PICTURE_WIDTH = 'PS_PRODUCT_PICTURE_WIDTH';
    public const PRODUCT_PICTURE_HEIGHT = 'PS_PRODUCT_PICTURE_HEIGHT';
    public const HIGHT_DPI = 'PS_HIGHT_DPI';
    public const LEGACY_IMAGES = 'PS_LEGACY_IMAGES';

    /** Merchandise return */
    public const ORDER_RETURN = 'PS_ORDER_RETURN';
    public const ORDER_RETURN_NB_DAYS = 'PS_ORDER_RETURN_NB_DAYS';
    public const RETURN_PREFIX = 'PS_RETURN_PREFIX';

    /** Customer Service */
    public const CUSTOMER_SERVICE_FILE_UPLOAD = 'PS_CUSTOMER_SERVICE_FILE_UPLOAD';
    public const CUSTOMER_SERVICE_SIGNATURE = 'PS_CUSTOMER_SERVICE_SIGNATURE';

    public const SAV_IMAP_URL = 'PS_SAV_IMAP_URL';
    public const SAV_IMAP_PORT = 'PS_SAV_IMAP_PORT';
    public const SAV_IMAP_USER = 'PS_SAV_IMAP_USER';
    public const SAV_IMAP_PWD = 'PS_SAV_IMAP_PWD';
    public const SAV_IMAP_DELETE_MSG = 'PS_SAV_IMAP_DELETE_MSG';
    public const SAV_IMAP_CREATE_THREADS = 'PS_SAV_IMAP_CREATE_THREADS';
    public const SAV_IMAP_OPT_POP3 = 'PS_SAV_IMAP_OPT_POP3';
    public const SAV_IMAP_OPT_NORSH = 'PS_SAV_IMAP_OPT_NORSH';
    public const SAV_IMAP_OPT_SSL = 'PS_SAV_IMAP_OPT_SSL';
    public const SAV_IMAP_OPT_VALIDATE = 'PS_SAV_IMAP_OPT_VALIDATE';
    public const SAV_IMAP_OPT_NOVALIDATE = 'PS_SAV_IMAP_OPT_NOVALIDATE';
    public const SAV_IMAP_OPT_TLS = 'PS_SAV_IMAP_OPT_TLS';
    public const SAV_IMAP_OPT_NOTLS = 'PS_SAV_IMAP_OPT_NOTLS';

    /** Invoices */
    public const INVOICE = 'PS_INVOICE';
    public const INVOICE_TAXES_BREAKDOWN = 'PS_INVOICE_TAXES_BREAKDOWN';
    public const PDF_IMG_INVOICE = 'PS_PDF_IMG_INVOICE';
    public const INVOICE_PREFIX = 'PS_INVOICE_PREFIX';
    public const INVOICE_USE_YEAR = 'PS_INVOICE_USE_YEAR';
    public const INVOICE_RESET = 'PS_INVOICE_RESET';
    public const INVOICE_YEAR_POS = 'PS_INVOICE_YEAR_POS';
    public const INVOICE_START_NUMBER = 'PS_INVOICE_START_NUMBER';
    public const INVOICE_LEGAL_FREE_TEXT = 'PS_INVOICE_LEGAL_FREE_TEXT';
    public const INVOICE_FREE_TEXT = 'PS_INVOICE_FREE_TEXT';
    public const INVOICE_MODEL = 'PS_INVOICE_MODEL';
    public const PDF_USE_CACHE = 'PS_PDF_USE_CACHE';

    /** Credit Slips */
    const CREDIT_SLIP_PREFIX = 'PS_CREDIT_SLIP_PREFIX';

    /** Delivery Slips */
    const DELIVERY_PREFIX = 'PS_DELIVERY_PREFIX';
    const DELIVERY_NUMBER = 'PS_DELIVERY_NUMBER';
    const PDF_IMG_DELIVERY = 'PS_PDF_IMG_DELIVERY';

    /** Used */
    public const INSTALL_VERSION =  'PS_INSTALL_VERSION';
    public const ADVANCED_STOCK_MANAGEMENT =  'PS_ADVANCED_STOCK_MANAGEMENT';

    public const STOCK_CUSTOMER_ORDER_CANCEL_REASON =  'PS_STOCK_CUSTOMER_ORDER_CANCEL_REASON';
    public const STOCK_CUSTOMER_RETURN_REASON =  'PS_STOCK_CUSTOMER_RETURN_REASON';
    public const STOCK_MVT_INC_EMPLOYEE_EDITION =  'PS_STOCK_MVT_INC_EMPLOYEE_EDITION';
    public const STOCK_MVT_DEC_EMPLOYEE_EDITION =  'PS_STOCK_MVT_DEC_EMPLOYEE_EDITION';
    public const STOCK_CUSTOMER_ORDER_REASON =  'PS_STOCK_CUSTOMER_ORDER_REASON';

    public const ACTIVE_CRONJOB_EXCHANGE_RATE =  'PS_ACTIVE_CRONJOB_EXCHANGE_RATE';

    public const CCCJS_VERSION =  'PS_CCCJS_VERSION';
    public const CCCCSS_VERSION =  'PS_CCCCSS_VERSION';
    public const REFERRERS_CACHE_LIKE =  'PS_REFERRERS_CACHE_LIKE';
    public const REFERRERS_CACHE_DATE =  'PS_REFERRERS_CACHE_DATE';
    public const TRACKING_DIRECT_TRAFFIC = 'TRACKING_DIRECT_TRAFFIC';

    /** @deprecated 1.5.5 */
    public const SHIPPING_METHOD =  'PS_SHIPPING_METHOD';

    /** SET `value` = '{"avoid":[]}' in 1.6.1.5 so I guess @deprecated  */
    public const INVCE_INVOICE_ADDR_RULES =  'PS_INVCE_INVOICE_ADDR_RULES';
    public const INVCE_DELIVERY_ADDR_RULES =  'PS_INVCE_DELIVERY_ADDR_RULES';

    /** Updated but not used */
    public const VERSION_DB =  'PS_VERSION_DB';

    /** Used but it seems no way to update */
    public const PASSWD_RESET_VALIDITY =  'PS_PASSWD_RESET_VALIDITY';
    public const STOCK_MVT_REASON_DEFAULT =  'PS_STOCK_MVT_REASON_DEFAULT';
    public const SPECIFIC_PRICE_PRIORITIES =  'PS_SPECIFIC_PRICE_PRIORITIES';
    public const STOCK_MVT_INC_REASON_DEFAULT =  'PS_STOCK_MVT_INC_REASON_DEFAULT';
    public const STOCK_MVT_DEC_REASON_DEFAULT =  'PS_STOCK_MVT_DEC_REASON_DEFAULT';
    public const STOCK_MVT_TRANSFER_TO =  'PS_STOCK_MVT_TRANSFER_TO';
    public const STOCK_MVT_TRANSFER_FROM =  'PS_STOCK_MVT_TRANSFER_FROM';
    public const ALLOW_MOBILE_DEVICE =  'PS_ALLOW_MOBILE_DEVICE';

    /** Not used but exists in configuration.xml */
    public const THEME_V11 =  'PS_THEME_V11';
    public const TIN_ACTIVE =  'PS_TIN_ACTIVE';
    public const SHOW_ALL_MODULES =  'PS_SHOW_ALL_MODULES';
    public const PS_1_3 = 'PS_1_3_2_UPDATE_DATE';
    public const PS_1_3_2 = 'UPDATE_DATE PS_1_3_2_UPDATE_DATE';
    public const BLOCK_BESTSELLERS_DISPLAY =  'PS_BLOCK_BESTSELLERS_DISPLAY';
    public const BLOCK_NEWPRODUCTS_DISPLAY =  'PS_BLOCK_NEWPRODUCTS_DISPLAY';
    public const BLOCK_SPECIALS_DISPLAY =  'PS_BLOCK_SPECIALS_DISPLAY';
    public const STORES_DISPLAY_CMS =  'PS_STORES_DISPLAY_CMS';
    public const STOCK_MVT_SUPPLY_ORDER =  'PS_STOCK_MVT_SUPPLY_ORDER';
    public const LOG_MODULE_PERFS_MODULO =  'PS_LOG_MODULE_PERFS_MODULO';
    public const DISPLAY_PRODUCT_WEIGHT =  'PS_DISPLAY_PRODUCT_WEIGHT';
    public const PRODUCT_WEIGHT_PRECISION =  'PS_PRODUCT_WEIGHT_PRECISION';
    public const CONFIGURATION_AGREMENT =  'PS_CONFIGURATION_AGREMENT';
    public const SEARCH_AJAX =  'PS_SEARCH_AJAX';


    /** @deprecated since 1.7.0 */
    public const CIPHER_ALGORITHM =  'PS_CIPHER_ALGORITHM';
    public const LOGO =  'PS_LOGO';


    /** Default order states */
    public const IMG_UPDATE_TIME =  'PS_IMG_UPDATE_TIME';
    public const OS_CHEQUE =  'PS_OS_CHEQUE';
    public const OS_PAYMENT =  'PS_OS_PAYMENT';
    public const OS_PREPARATION =  'PS_OS_PREPARATION';
    public const OS_SHIPPING =  'PS_OS_SHIPPING';
    public const OS_DELIVERED =  'PS_OS_DELIVERED';
    public const OS_CANCELED =  'PS_OS_CANCELED';
    public const OS_REFUND =  'PS_OS_REFUND';
    public const OS_ERROR =  'PS_OS_ERROR';
    public const OS_OUTOFSTOCK =  'PS_OS_OUTOFSTOCK';
    public const OS_BANKWIRE =  'PS_OS_BANKWIRE';
    public const OS_WS_PAYMENT =  'PS_OS_WS_PAYMENT';
    public const OS_OUTOFSTOCK_PAID =  'PS_OS_OUTOFSTOCK_PAID';
    public const OS_OUTOFSTOCK_UNPAID =  'PS_OS_OUTOFSTOCK_UNPAID';
    public const OS_COD_VALIDATION =  'PS_OS_COD_VALIDATION';


    /** Active if used */
    public const SPECIFIC_PRICE_FEATURE_ACTIVE =  'PS_SPECIFIC_PRICE_FEATURE_ACTIVE';
    public const VIRTUAL_PROD_FEATURE_ACTIVE =  'PS_VIRTUAL_PROD_FEATURE_ACTIVE';
    public const CUSTOMIZATION_FEATURE_ACTIVE =  'PS_CUSTOMIZATION_FEATURE_ACTIVE';
    public const CART_RULE_FEATURE_ACTIVE =  'PS_CART_RULE_FEATURE_ACTIVE';
    public const PACK_FEATURE_ACTIVE =  'PS_PACK_FEATURE_ACTIVE';
    public const ALIAS_FEATURE_ACTIVE =  'PS_ALIAS_FEATURE_ACTIVE';


    /** Module stats */
    public const STATS_RENDER =  'PS_STATS_RENDER';
    public const STATS_OLD_CONNECT_AUTO_CLEAN =  'PS_STATS_OLD_CONNECT_AUTO_CLEAN';
    public const STATS_GRID_RENDER =  'PS_STATS_GRID_RENDER';

    public const BASE_DISTANCE_UNIT =  'PS_BASE_DISTANCE_UNIT';
    public const FAVICON =  'PS_FAVICON';
    public const STORES_ICON =  'PS_STORES_ICON';

    /** Categories  */
    public const ROOT_CATEGORY =  'PS_ROOT_CATEGORY';
    public const HOME_CATEGORY =  'PS_HOME_CATEGORY';
    public const MAIL_COLOR =  'PS_MAIL_COLOR';

    public const MAIL_THEME =  'PS_MAIL_THEME';

    /** Dashboard */
    public const DASHBOARD_USE_PUSH =  'PS_DASHBOARD_USE_PUSH';
    public const DASHBOARD_SIMULATION =  'PS_DASHBOARD_SIMULATION';
    public const CONF_AVERAGE_PRODUCT_MARGIN = 'CONF_AVERAGE_PRODUCT_MARGIN';

    /** Configurations without PS in their name that seem to be updated but not used
    SHOP_LOGO_WIDTH
    SHOP_LOGO_HEIGHT
    EDITORIAL_IMAGE_WIDTH
    EDITORIAL_IMAGE_HEIGHT
     */

    /** Configurations without PS in their name that are not used(but still exist in configurations.xml)
    MB_PAY_TO_EMAIL
    MB_SECRET_WORD
    MB_HIDE_LOGIN
    MB_ID_LOGO
    MB_ID_LOGO_WALLET
    MB_PARAMETERS
    MB_PARAMETERS_2
    MB_DISPLAY_MODE
    MB_CANCEL_URL
    MB_LOCAL_METHODS
    MB_INTER_METHODS
    BANK_WIRE_CURRENCIES
    CHEQUE_CURRENCIES
    BLOCK_CATEG_DHTML
    MANUFACTURER_DISPLAY_FORM
    MANUFACTURER_DISPLAY_TEXT
    MANUFACTURER_DISPLAY_TEXT_NB
    BLOCKTAGS_NBR
    //Not used, deleted on upgrade 3 by linklist
    FOOTER_CMS
    FOOTER_BLOCK_ACTIVATION
    FOOTER_POWEREDBY
    BLOCKADVERT_LINK
    BLOCKSTORE_IMG
    BLOCKADVERT_IMG_EXT
    MOD_BLOCKTOPMENU_SEARCH
    BLOCKCONTACTINFOS_COMPANY
    BLOCKCONTACTINFOS_ADDRESS
    BLOCKCONTACTINFOS_PHONE
    BLOCKCONTACTINFOS_EMAIL
    BLOCKCONTACT_TELNUMBER
    BLOCKCONTACT_EMAIL
    SUPPLIER_DISPLAY_TEXT
    SUPPLIER_DISPLAY_TEXT_NB
    SUPPLIER_DISPLAY_FORM
    BLOCK_CATEG_NBR_COLUMN_FOOTER
    UPGRADER_BACKUPDB_FILENAME
    UPGRADER_BACKUPFILES_FILENAME
    BLOCKREINSURANCE_NBBLOCKS
    HOMESLIDER_WIDTH

    // Seems to be used even tough deleted in an upgrade;
    HOMESLIDER_PAUSE
    HOMESLIDER_LOOP
    PS_PAYMENT_LOGO_CMS_ID
     */


    public $id;

    /** @var string Key */
    public $name;

    public $id_shop_group;
    public $id_shop;

    /** @var string Value */
    public $value;

    /** @var string Object creation date */
    public $date_add;

    /** @var string Object last modification date */
    public $date_upd;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'configuration',
        'primary' => 'id_configuration',
        'multilang' => true,
        'fields' => [
            'name' => ['type' => self::TYPE_STRING, 'validate' => 'isConfigName', 'required' => true, 'size' => 254],
            'id_shop_group' => ['type' => self::TYPE_NOTHING, 'validate' => 'isUnsignedId'],
            'id_shop' => ['type' => self::TYPE_NOTHING, 'validate' => 'isUnsignedId'],
            'value' => ['type' => self::TYPE_STRING],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'date_upd' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
        ],
    ];

    /** @var array Configuration cache (kept for backward compat) */
    protected static $_cache = null;

    /** @var array Configuration cache with optimised key order */
    protected static $_new_cache_shop = null;
    protected static $_new_cache_group = null;
    protected static $_new_cache_global = null;
    protected static $_initialized = false;

    /** @var array Vars types */
    protected static $types = [];

    protected $webserviceParameters = [
        'fields' => [
            'value' => [],
        ],
    ];

    /**
     * @see ObjectModel::getFieldsLang()
     *
     * @return bool|array Multilingual fields
     */
    public function getFieldsLang()
    {
        if (!is_array($this->value)) {
            return true;
        }

        return parent::getFieldsLang();
    }

    /**
     * Return ID a configuration key.
     *
     * @param string $key
     * @param int $idShopGroup
     * @param int $idShop
     *
     * @return int Configuration key ID
     */
    public static function getIdByName($key, $idShopGroup = null, $idShop = null)
    {
        if ($idShop === null) {
            $idShop = Shop::getContextShopID(true);
        }
        if ($idShopGroup === null) {
            $idShopGroup = Shop::getContextShopGroupID(true);
        }

        $sql = 'SELECT `' . bqSQL(self::$definition['primary']) . '`
                FROM `' . _DB_PREFIX_ . bqSQL(self::$definition['table']) . '`
                WHERE name = \'' . pSQL($key) . '\'
                ' . Configuration::sqlRestriction($idShopGroup, $idShop);

        return (int) Db::getInstance()->getValue($sql);
    }

    /**
     * Is the configuration loaded.
     *
     * @return bool `true` if configuration is loaded
     */
    public static function configurationIsLoaded()
    {
        return self::$_initialized;
    }

    /**
     * WARNING: For testing only. Do NOT rely on this method, it may be removed at any time.
     *
     * @todo Delegate static calls from Configuration to an instance
     * of a class to be created.
     */
    public static function clearConfigurationCacheForTesting()
    {
        self::$_cache = null;
        self::$_new_cache_shop = null;
        self::$_new_cache_group = null;
        self::$_new_cache_global = null;
        self::$_initialized = false;
    }

    /**
     * Load all configuration data.
     */
    public static function loadConfiguration()
    {
        $sql = 'SELECT c.`name`, cl.`id_lang`, IF(cl.`id_lang` IS NULL, c.`value`, cl.`value`) AS value, c.id_shop_group, c.id_shop
               FROM `' . _DB_PREFIX_ . bqSQL(self::$definition['table']) . '` c
               LEFT JOIN `' . _DB_PREFIX_ . bqSQL(self::$definition['table']) . '_lang` cl ON (c.`' . bqSQL(
               self::$definition['primary']
            ) . '` = cl.`' . bqSQL(self::$definition['primary']) . '`)';
        $db = Db::getInstance();
        $results = $db->executeS($sql);
        if ($results) {
            foreach ($results as $row) {
                $lang = ($row['id_lang']) ? $row['id_lang'] : 0;
                self::$types[$row['name']] = (bool) $lang;

                if (!isset(self::$_cache[self::$definition['table']][$lang])) {
                    self::$_cache[self::$definition['table']][$lang] = [
                        'global' => [],
                        'group' => [],
                        'shop' => [],
                    ];
                }

                if ($row['value'] === null) {
                    $row['value'] = '';
                }

                if ($row['id_shop']) {
                    self::$_cache[self::$definition['table']][$lang]['shop'][$row['id_shop']][$row['name']] = $row['value'];
                    self::$_new_cache_shop[$row['name']][$lang][$row['id_shop']] = $row['value'];
                } elseif ($row['id_shop_group']) {
                    self::$_cache[self::$definition['table']][$lang]['group'][$row['id_shop_group']][$row['name']] = $row['value'];
                    self::$_new_cache_group[$row['name']][$lang][$row['id_shop_group']] = $row['value'];
                } else {
                    self::$_cache[self::$definition['table']][$lang]['global'][$row['name']] = $row['value'];
                    self::$_new_cache_global[$row['name']][$lang] = $row['value'];
                }
            }
            self::$_initialized = true;
        }
    }

    /**
     * Get a single configuration value (in one language only).
     *
     * @param string $key Key wanted
     * @param int $idLang Language ID
     *
     * @return string|false Value
     */
    public static function get($key, $idLang = null, $idShopGroup = null, $idShop = null, $default = false)
    {
        if (defined('_PS_DO_NOT_LOAD_CONFIGURATION_') && _PS_DO_NOT_LOAD_CONFIGURATION_) {
            return false;
        }

        // Init the cache on demand
        if (!self::$_initialized) {
            Configuration::loadConfiguration();
        }
        $idLang = (int) $idLang;

        if (!self::isLangKey($key)) {
            $idLang = 0;
        }

        if (self::$_new_cache_shop === null) {
            $idShop = 0;
        } else {
            if ($idShop === null || !Shop::isFeatureActive()) {
                $idShop = Shop::getContextShopID(true);
            }
        }

        if (self::$_new_cache_group === null) {
            $idShopGroup = 0;
        } else {
            if ($idShopGroup === null || !Shop::isFeatureActive()) {
                $idShopGroup = Shop::getContextShopGroupID(true);
            }
        }

        if ($idShop && Configuration::hasKey($key, $idLang, null, $idShop)) {
            return self::$_new_cache_shop[$key][$idLang][$idShop];
        } elseif ($idShopGroup && Configuration::hasKey($key, $idLang, $idShopGroup)) {
            return self::$_new_cache_group[$key][$idLang][$idShopGroup];
        } elseif (Configuration::hasKey($key, $idLang)) {
            return self::$_new_cache_global[$key][$idLang];
        }

        return $default;
    }

    /**
     * Get global value.
     *
     * @param string $key Configuration key
     * @param int|null $idLang Language ID
     *
     * @return string
     */
    public static function getGlobalValue($key, $idLang = null)
    {
        return Configuration::get($key, $idLang, 0, 0);
    }

    /**
     * @deprecated use Configuration::getConfigInMultipleLangs() instead
     */
    public static function getInt($key, $idShopGroup = null, $idShop = null)
    {
        return self::getConfigInMultipleLangs($key, $idShopGroup, $idShop);
    }

    /**
     * Get a single configuration value (in multiple languages).
     *
     * @param string $key Configuration Key
     * @param int $idShopGroup Shop Group ID
     * @param int $idShop Shop ID
     *
     * @return array Values in multiple languages
     */
    public static function getConfigInMultipleLangs($key, $idShopGroup = null, $idShop = null)
    {
        $resultsArray = [];
        foreach (Language::getIDs() as $idLang) {
            $resultsArray[$idLang] = Configuration::get($key, $idLang, $idShopGroup, $idShop);
        }

        return $resultsArray;
    }

    /**
     * Get a single configuration value for all shops.
     *
     * @param string $key Key wanted
     * @param int $idLang
     *
     * @return array Values for all shops
     */
    public static function getMultiShopValues($key, $idLang = null)
    {
        $shops = Shop::getShops(false, null, true);
        $resultsArray = [];
        foreach ($shops as $idShop) {
            $resultsArray[$idShop] = Configuration::get($key, $idLang, null, $idShop);
        }

        return $resultsArray;
    }

    /**
     * Get several configuration values (in one language only).
     *
     * @throws PrestaShopException
     *
     * @param array $keys Keys wanted
     * @param int $idLang Language ID
     * @param int $idShopGroup
     * @param int $idShop
     *
     * @return array Values
     */
    public static function getMultiple($keys, $idLang = null, $idShopGroup = null, $idShop = null)
    {
        if (!is_array($keys)) {
            throw new PrestaShopException('keys var is not an array');
        }

        $idLang = (int) $idLang;
        if ($idShop === null) {
            $idShop = Shop::getContextShopID(true);
        }
        if ($idShopGroup === null) {
            $idShopGroup = Shop::getContextShopGroupID(true);
        }

        $results = [];
        foreach ($keys as $key) {
            $results[$key] = Configuration::get($key, $idLang, $idShopGroup, $idShop);
        }

        return $results;
    }

    /**
     * Check if key exists in configuration.
     *
     * @param string $key
     * @param int $idLang
     * @param int $idShopGroup
     * @param int $idShop
     *
     * @return bool
     */
    public static function hasKey($key, $idLang = null, $idShopGroup = null, $idShop = null)
    {
        if (!is_int($key) && !is_string($key)) {
            return false;
        }

        $idLang = (int) $idLang;

        if ($idShop) {
            return isset(self::$_new_cache_shop[$key][$idLang][$idShop]);
        } elseif ($idShopGroup) {
            return isset(self::$_new_cache_group[$key][$idLang][$idShopGroup]);
        }

        return isset(self::$_new_cache_global[$key][$idLang]);
    }

    /**
     * Set TEMPORARY a single configuration value (in one language only).
     *
     * @param string $key Configuration key
     * @param mixed $values `$values` is an array if the configuration is multilingual, a single string else
     * @param int $idShopGroup
     * @param int $idShop
     */
    public static function set($key, $values, $idShopGroup = null, $idShop = null)
    {
        if (!Validate::isConfigName($key)) {
            die(Tools::displayError(Context::getContext()->getTranslator()->trans('[%s] is not a valid configuration key', [Tools::htmlentitiesUTF8($key)], 'Admin.Notifications.Error')));
        }

        if ($idShop === null) {
            $idShop = (int) Shop::getContextShopID(true);
        }
        if ($idShopGroup === null) {
            $idShopGroup = (int) Shop::getContextShopGroupID(true);
        }

        if (!is_array($values)) {
            $values = [$values];
        }

        foreach ($values as $lang => $value) {
            if ($idShop) {
                self::$_new_cache_shop[$key][$lang][$idShop] = $value;
                self::$_cache[self::$definition['table']][$lang]['shop'][$idShop][$key] = $value;
            } elseif ($idShopGroup) {
                self::$_new_cache_group[$key][$lang][$idShopGroup] = $value;
                self::$_cache[self::$definition['table']][$lang]['group'][$idShopGroup][$key] = $value;
            } else {
                self::$_new_cache_global[$key][$lang] = $value;
                self::$_cache[self::$definition['table']][$lang]['global'][$key] = $value;
            }
        }
    }

    /**
     * Update configuration key for global context only.
     *
     * @param string $key
     * @param mixed $values
     * @param bool $html
     *
     * @return bool
     */
    public static function updateGlobalValue($key, $values, $html = false)
    {
        return Configuration::updateValue($key, $values, $html, 0, 0);
    }

    /**
     * Update configuration key and value into database (automatically insert if key does not exist).
     *
     * Values are inserted/updated directly using SQL, because using (Configuration) ObjectModel
     * may not insert values correctly (for example, HTML is escaped, when it should not be).
     *
     * @TODO Fix saving HTML values in Configuration model
     *
     * @param string $key Configuration key
     * @param mixed $values $values is an array if the configuration is multilingual, a single string else
     * @param bool $html Specify if html is authorized in value
     * @param int $idShopGroup
     * @param int $idShop
     *
     * @return bool Update result
     */
    public static function updateValue($key, $values, $html = false, $idShopGroup = null, $idShop = null)
    {
        if (!Validate::isConfigName($key)) {
            die(Tools::displayError(Context::getContext()->getTranslator()->trans('[%s] is not a valid configuration key', [Tools::htmlentitiesUTF8($key)], 'Admin.Notifications.Error')));
        }

        if ($idShop === null || !Shop::isFeatureActive()) {
            $idShop = Shop::getContextShopID(true);
        }
        if ($idShopGroup === null || !Shop::isFeatureActive()) {
            $idShopGroup = Shop::getContextShopGroupID(true);
        }

        if (!is_array($values)) {
            $values = [$values];
        }

        if ($html) {
            $values = array_map(function ($v) {
                return Tools::purifyHTML($v);
            }, $values);
        }

        $result = true;
        foreach ($values as $lang => $value) {
            $storedValue = Configuration::get($key, $lang, $idShopGroup, $idShop);
            // if there isn't a $stored_value, we must insert $value
            if ((!is_numeric($value) && $value === $storedValue) || (is_numeric($value) && $value == $storedValue && Configuration::hasKey($key, $lang))) {
                continue;
            }

            // If key already exists, update value
            if (Configuration::hasKey($key, $lang, $idShopGroup, $idShop)) {
                if (!$lang) {
                    // Update config not linked to lang
                    $result &= Db::getInstance()->update(self::$definition['table'], [
                        'value' => pSQL($value, $html),
                        'date_upd' => date('Y-m-d H:i:s'),
                    ], '`name` = \'' . pSQL($key) . '\'' . Configuration::sqlRestriction($idShopGroup, $idShop), 1, true);
                } else {
                    // Update multi lang
                    $sql = 'UPDATE `' . _DB_PREFIX_ . bqSQL(self::$definition['table']) . '_lang` cl
                            SET cl.value = \'' . pSQL($value, $html) . '\',
                                cl.date_upd = NOW()
                            WHERE cl.id_lang = ' . (int) $lang . '
                                AND cl.`' . bqSQL(self::$definition['primary']) . '` = (
                                    SELECT c.`' . bqSQL(self::$definition['primary']) . '`
                                    FROM `' . _DB_PREFIX_ . bqSQL(self::$definition['table']) . '` c
                                    WHERE c.name = \'' . pSQL($key) . '\''
                                        . Configuration::sqlRestriction($idShopGroup, $idShop)
                                . ')';
                    $result &= Db::getInstance()->execute($sql);
                }
            } else {
                // If key does not exists, create it
                if (!$configID = Configuration::getIdByName($key, $idShopGroup, $idShop)) {
                    $now = date('Y-m-d H:i:s');
                    $data = [
                        'id_shop_group' => $idShopGroup ? (int) $idShopGroup : null,
                        'id_shop' => $idShop ? (int) $idShop : null,
                        'name' => pSQL($key),
                        'value' => $lang ? null : pSQL($value, $html),
                        'date_add' => $now,
                        'date_upd' => $now,
                    ];
                    $result &= Db::getInstance()->insert(self::$definition['table'], $data, true);
                    $configID = Db::getInstance()->Insert_ID();
                }

                if ($lang) {
                    $table = self::$definition['table'] . '_lang';
                    $selectConfiguration = strtr(
                        'SELECT 1 FROM {{ table }} WHERE id_lang = {{ lang }} ' .
                        'AND `{{ primary_key_column }}` = {{ config_id }}',
                        [
                            '{{ table }}' => _DB_PREFIX_ . $table,
                            '{{ lang }}' => (int) $lang,
                            '{{ primary_key_column }}' => self::$definition['primary'],
                            '{{ config_id }}' => $configID,
                        ]
                    );
                    $results = Db::getInstance()->getRow($selectConfiguration);
                    $configurationExists = is_array($results) && count($results) > 0;
                    $now = date('Y-m-d H:i:s');
                    $sanitizedValue = pSQL($value, $html);

                    if ($configurationExists) {
                        $condition = strtr(
                            '`{{ primary_key_column }}` = {{ config_id }} AND ' .
                            'date_upd = "{{ update_date }}" AND ' .
                            'value = "{{ value }}"',
                            [
                                '{{ primary_key_column }}' => self::$definition['primary'],
                                '{{ config_id }}' => $configID,
                                '{{ update_date }}' => $now,
                                '{{ value }}' => $sanitizedValue,
                            ]
                        );
                        $result &= Db::getInstance()->update($table, [
                            'value' => $sanitizedValue,
                            'date_upd' => date('Y-m-d H:i:s'),
                        ], $condition, 1, true);
                    } else {
                        $result &= Db::getInstance()->insert($table, [
                            self::$definition['primary'] => $configID,
                            'id_lang' => (int) $lang,
                            'value' => $sanitizedValue,
                            'date_upd' => $now,
                        ]);
                    }
                }
            }
        }

        Configuration::set($key, $values, $idShopGroup, $idShop);

        return (bool) $result;
    }

    /**
     * Delete a configuration key in database (with or without language management).
     *
     * @param string $key Key to delete
     *
     * @return bool Deletion result
     */
    public static function deleteByName($key)
    {
        if (!Validate::isConfigName($key)) {
            return false;
        }

        $result = Db::getInstance()->execute('
        DELETE FROM `' . _DB_PREFIX_ . bqSQL(self::$definition['table']) . '_lang`
        WHERE `' . bqSQL(self::$definition['primary']) . '` IN (
            SELECT `' . bqSQL(self::$definition['primary']) . '`
            FROM `' . _DB_PREFIX_ . bqSQL(self::$definition['table']) . '`
            WHERE `name` = "' . pSQL($key) . '"
        )');

        $result2 = Db::getInstance()->execute('
        DELETE FROM `' . _DB_PREFIX_ . bqSQL(self::$definition['table']) . '`
        WHERE `name` = "' . pSQL($key) . '"');

        self::$_cache = null;
        self::$_new_cache_shop = null;
        self::$_new_cache_group = null;
        self::$_new_cache_global = null;
        self::$_initialized = false;

        return $result && $result2;
    }

    /**
     * Delete configuration key from current context.
     *
     * @param string $key
     */
    public static function deleteFromContext($key)
    {
        if (Shop::getContext() == Shop::CONTEXT_ALL) {
            return;
        }

        $idShop = null;
        $idShopGroup = Shop::getContextShopGroupID(true);
        if (Shop::getContext() == Shop::CONTEXT_SHOP) {
            $idShop = Shop::getContextShopID(true);
        }

        $id = Configuration::getIdByName($key, $idShopGroup, $idShop);
        Db::getInstance()->execute('
        DELETE FROM `' . _DB_PREFIX_ . bqSQL(self::$definition['table']) . '`
        WHERE `' . bqSQL(self::$definition['primary']) . '` = ' . (int) $id);
        Db::getInstance()->execute('
        DELETE FROM `' . _DB_PREFIX_ . bqSQL(self::$definition['table']) . '_lang`
        WHERE `' . bqSQL(self::$definition['primary']) . '` = ' . (int) $id);

        self::$_cache = null;
        self::$_new_cache_shop = null;
        self::$_new_cache_group = null;
        self::$_new_cache_global = null;
        self::$_initialized = false;
    }

    /**
     * Check if configuration var is defined in given context.
     *
     * @param string $key
     * @param int $idLang
     * @param int $context
     */
    public static function hasContext($key, $idLang, $context)
    {
        if (Shop::getContext() == Shop::CONTEXT_ALL) {
            $idShop = $idShopGroup = null;
        } elseif (Shop::getContext() == Shop::CONTEXT_GROUP) {
            $idShopGroup = Shop::getContextShopGroupID(true);
            $idShop = null;
        } else {
            $idShopGroup = Shop::getContextShopGroupID(true);
            $idShop = Shop::getContextShopID(true);
        }

        if ($context == Shop::CONTEXT_SHOP && Configuration::hasKey($key, $idLang, null, $idShop)) {
            return true;
        } elseif ($context == Shop::CONTEXT_GROUP && Configuration::hasKey($key, $idLang, $idShopGroup)) {
            return true;
        } elseif ($context == Shop::CONTEXT_ALL && Configuration::hasKey($key, $idLang)) {
            return true;
        }

        return false;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public static function isOverridenByCurrentContext($key)
    {
        if (Configuration::isLangKey($key)) {
            $testContext = false;
            foreach (Language::getIDs(false) as $idLang) {
                if ((Shop::getContext() == Shop::CONTEXT_SHOP && Configuration::hasContext($key, $idLang, Shop::CONTEXT_SHOP))
                    || (Shop::getContext() == Shop::CONTEXT_GROUP && Configuration::hasContext($key, $idLang, Shop::CONTEXT_GROUP))) {
                    $testContext = true;
                }
            }
        } else {
            $testContext = ((Shop::getContext() == Shop::CONTEXT_SHOP && Configuration::hasContext($key, null, Shop::CONTEXT_SHOP))
                            || (Shop::getContext() == Shop::CONTEXT_GROUP && Configuration::hasContext($key, null, Shop::CONTEXT_GROUP))) ? true : false;
        }

        return Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_ALL && $testContext;
    }

    /**
     * Check if a key was loaded as multi lang.
     *
     * @param string $key
     *
     * @return bool
     */
    public static function isLangKey($key)
    {
        return isset(self::$types[$key]) && self::$types[$key];
    }

    /**
     * @return bool
     */
    public static function isCatalogMode()
    {
        if (is_a(Context::getContext()->controller, 'FrontController')) {
            $isCatalogMode =
                Configuration::get('PS_CATALOG_MODE') ||
                !Configuration::showPrices() ||
                (Context::getContext()->controller->getRestrictedCountry() == Country::GEOLOC_CATALOG_MODE);
        } else {
            $isCatalogMode =
                Configuration::get('PS_CATALOG_MODE') ||
                !Configuration::showPrices();
        }

        return $isCatalogMode;
    }

    /**
     * @return bool
     */
    public static function showPrices()
    {
        return Group::isFeatureActive() ? (bool) Group::getCurrent()->show_prices : true;
    }

    /**
     * Add SQL restriction on shops for configuration table.
     *
     * @param int $idShopGroup
     * @param int $idShop
     *
     * @return string
     */
    protected static function sqlRestriction($idShopGroup, $idShop)
    {
        if ($idShop) {
            return ' AND id_shop = ' . (int) $idShop;
        } elseif ($idShopGroup) {
            return ' AND id_shop_group = ' . (int) $idShopGroup . ' AND (id_shop IS NULL OR id_shop = 0)';
        } else {
            return ' AND (id_shop_group IS NULL OR id_shop_group = 0) AND (id_shop IS NULL OR id_shop = 0)';
        }
    }

    /**
     * This method is override to allow TranslatedConfiguration entity.
     *
     * @param string $sqlJoin
     * @param string $sqlFilter
     * @param string $sqlSort
     * @param string $sqlLimit
     *
     * @return array
     */
    public function getWebserviceObjectList($sqlJoin, $sqlFilter, $sqlSort, $sqlLimit)
    {
        $query = '
        SELECT DISTINCT main.`' . bqSQL($this->def['primary']) . '`
        FROM `' . _DB_PREFIX_ . bqSQL($this->def['table']) . '` main
        ' . $sqlJoin . '
        WHERE id_configuration NOT IN (
            SELECT id_configuration
            FROM `' . _DB_PREFIX_ . bqSQL($this->def['table']) . '_lang`
        ) ' . $sqlFilter . '
        ' . ($sqlSort != '' ? $sqlSort : '') . '
        ' . ($sqlLimit != '' ? $sqlLimit : '');

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
    }
}
