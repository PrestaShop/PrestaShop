<?php

/**
 * Définition des montants sur lesquels s'appliquent les taxes
 *
 */
define('HIPAY_MAPI_TTARGET_TAX', 1);
define('HIPAY_MAPI_TTARGET_INSURANCE', 2);
define('HIPAY_MAPI_TTARGET_FCOST', 4);
define('HIPAY_MAPI_TTARGET_SHIPPING', 8);
define('HIPAY_MAPI_TTARGET_ITEM', 16);
define('HIPAY_MAPI_TTARGET_ALL', HIPAY_MAPI_TTARGET_TAX+HIPAY_MAPI_TTARGET_INSURANCE+HIPAY_MAPI_TTARGET_FCOST+HIPAY_MAPI_TTARGET_SHIPPING+HIPAY_MAPI_TTARGET_ITEM);

/**
 * Type de paiements possibles
 *
 */
define('HIPAY_MAPI_METHOD_SIMPLE', 0);
define('HIPAY_MAPI_METHOD_MULTI', 1);

/**
 * Valeurs par défaut
 *
 */
define('HIPAY_MAPI_DEFLANG', 'FR_fr');
define('HIPAY_MAPI_DEFMEDIA', 'WEB');

define('HIPAY_MAPI_MAX_INFO_LENGTH', 200);
define('HIPAY_MAPI_MAX_TITLE_LENGTH', 80);

define('HIPAY_MAPI_MAX_LOGIN_LENGTH', 20);
define('HIPAY_MAPI_MAX_PASSWORD_LENGTH', 20);

define('HIPAY_MAPI_MAX_RATING_LENGTH', 8);

define('HIPAY_MAPI_MAX_MDATAS_LENGTH', 200);

define('HIPAY_MAPI_MAX_ACKWD_LENGTH', 8);

define('HIPAY_MAPI_MAX_ACKMAIL_LENGTH', 64);

define('HIPAY_MAPI_MAX_PRODUCT_NAME_LENGTH', 100);

define('HIPAY_MAPI_MAX_PRODUCT_INFO_LENGTH', 100);

define('HIPAY_MAPI_MAX_PRODUCT_REF_LENGTH', 35);

define('HIPAY_MAPI_MAX_TAX_NAME_LENGTH', 32);

/**
 * Valeurs particulières pour le délai de capture
 *
 */
define('HIPAY_MAPI_CAPTURE_MANUAL', -1);
define('HIPAY_MAPI_CAPTURE_IMMEDIATE', 0);
define('HIPAY_MAPI_CAPTURE_MAX_DAYS', 7);

define('HIPAY_MAPI_OPE_PREAUTH', 'preauthorization');
define('HIPAY_MAPI_OPE_AUTH', 'authorization');
define('HIPAY_MAPI_OPE_CANCEL', 'cancellation');
define('HIPAY_MAPI_OPE_REFUND', 'refund');
define('HIPAY_MAPI_OPE_CAPTURE', 'capture');
define('HIPAY_MAPI_OPE_REJECT', 'rejet');
define('HIPAY_MAPI_STATUS_OK', 'ok');
define('HIPAY_MAPI_STATUS_NOK', 'nok');

// Nombre de secondes avant le timeout de curl avec le serveur Hipay
// A régler en fonction de votre rapidité d'accès à la plate forme hipay
define('HIPAY_MAPI_CURL_TIMEOUT', 30);

// Configuration d'un serveur proxy
// activer cette option a true pour demander au curl de passer par un proxy
define('HIPAY_MAPI_CURL_PROXY_ON', false);
// Adresse du proxy
define('HIPAY_MAPI_CURL_PROXY', 'http://');
// port du proxy
define('HIPAY_MAPI_CURL_PROXYPORT', '');

// Configuration d'un fichier de log pour curl en cas de pb de connexion avec le serveur Hipay
define('HIPAY_MAPI_CURL_LOG_ON', false);
// fichier de log de curl (sous environnement windows, le chemin du fichier pourra être de type C:\tmp\mapicurl.log)
define('HIPAY_MAPI_CURL_LOGFILE', '/tmp/curl.log');
define('MAPI_VERSION','1.0');