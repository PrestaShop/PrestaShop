<?php
die;
foreach (scandir('.') as $dir)
	if ($dir[0] != '.')
		foreach (scandir($dir) as $file)
		{
		if ($file[0] == '.')
				continue;
				$name = $file;
			foreach (array('small', 'large', 'medium', 'thickbox', 'category', 'home') as $type)
				$name = str_replace($type, $type.'_default', $name);
			$name = str_replace('large_scene', 'scene_default', $name);
			$name = str_replace('thumb_scene', 'm_scene_default', $name);
			
			echo($dir.'/'.$file);
		echo($dir.'/'.$name);
		rename($dir.'/'.$file, $dir.'/'.$name);
		}
