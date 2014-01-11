{strip}
{addJsDef baseDir=$content_dir}
{addJsDef baseUri=$base_uri}
{addJsDef static_token=$static_token}
{addJsDef token=$token}
{addJsDef priceDisplayPrecision=$priceDisplayPrecision*$currency->decimals}
{addJsDef priceDisplayMethod=$priceDisplay}
{addJsDef roundMode=$roundMode}
{addJsDef logged=$logged|boolval}
{addJsDef page_name=$page_name|escape:'html':'UTF-8'}
{addJsDef contentOnly=$content_only|boolval}
{if isset($cookie->id_lang)}
	{addJsDef id_lang=$cookie->id_lang|intval}
{/if}
{addJsDef usingSecureMode=Tools::usingSecureMode()|boolval}
{addJsDef ajaxsearch=Configuration::get('PS_SEARCH_AJAX')|boolval}
{addJsDef instantsearch=Configuration::get('PS_INSTANT_SEARCH')|boolval}
{/strip}