<?php

function drop_image_type_non_unique_index()
{
	$index = Db::getInstance()->executeS('SHOW index FROM ps_image_type where column_name = "name" and non_unique=1');
	// do not use pSql, this function is not defined
	Db::getInstance()->execute('ALTER TABLE `PREFIX_image_type` DROP INDEX "'.$index.'"');
}
