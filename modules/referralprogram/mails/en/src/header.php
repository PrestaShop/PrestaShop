<?php
	if(!function_exists('t'))
	{
	function t($str)
		{
			return $str;
		}
	}

	if(!function_exists('findRelativePathToAdminDir'))
	{
		function findRelativePathToAdminDir($maxDepth=6)
		{
			$path = '';
			for($i=0; $i<$maxDepth; $i++)
			{
				foreach(scandir(dirname(__FILE__).'/'.$path) as $dir)
				{
					$candidate = $path.$dir.'/';
					if(is_dir(dirname(__FILE__).'/'.$candidate.'tabs'))
					{
						return $candidate;
					}
				}
				$path .= '../';
			}

			return false;
		}
	}

	$admin = findRelativePathToAdminDir(); 
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/strict.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title><?php echo t('Message from {shop_name}'); ?></title>
		<link rel="stylesheet" type="text/css" href="<?php echo $admin ?>themes/default/css/admin-theme/email.css">
	</head>
	<body>
		<table class="table table-mail">
			<tr>
				<td class="space">&nbsp;</td>
				<td align="center">
					<table class="table">
						<tr>
							<td align="center" class="logo">
								<a title="{shop_name}" href="{shop_url}">
									<img src="{shop_logo}" alt="{shop_name}" />
								</a>
							</td>
						</tr>
