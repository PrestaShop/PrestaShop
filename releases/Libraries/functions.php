<?php

if (function_exists('array_flattenk') === false)
{
	function array_flattenk($items)
	{
		$results = [];

		foreach ($items as $key => $value) {
			if (is_array($value) == true) {
				$results[] = $key;
				$results = array_merge($results, array_flattenk($value));
			} else {
				$results[] = $value;
			}
		}

		ksort($results);

		return $results;
	}
}

if (function_exists('convert') === false)
{
	function convert($size)
	{
		$unit = array('b','kb','mb','gb','tb','pb');
		return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
	}
}

if (function_exists('get_directory_structure') === false)
{
	function get_directory_structure($path)
	{
		$flags = FilesystemIterator::SKIP_DOTS | RecursiveIteratorIterator::CHILD_FIRST;
		$iterator = new RecursiveDirectoryIterator($path, $flags);
		$childrens = iterator_count($iterator);

		$structure = [];

		if ($childrens > 0) {
			$children = $iterator->getChildren();

			for ($index = 0; $index < $childrens; $index += 1) {
				$pathname = $children->getPathname();
				$subpathname = trim($children->getSubPathname(), '/');

				if ($children->hasChildren() === true) {
					$structure[$pathname] = get_directory_structure($pathname);
				} else {
					$structure[] = $pathname;
				}

				$children->next();
			}
		}

		ksort($structure);

		return $structure;
	}
}

if (function_exists('get_directory_structure_flatten') === false)
{
	function get_directory_structure_flatten($path, $preserve_keys = false)
	{
		$structure = [];
		$flags = FilesystemIterator::SKIP_DOTS | RecursiveIteratorIterator::CHILD_FIRST;
		$iterator = new RecursiveDirectoryIterator($path, $flags);
		$childrens = iterator_count($iterator);

		if ($childrens > 0) {
			$children = $iterator->getChildren();

			for ($index = 0; $index < $childrens; $index += 1) {
				$pathname = $children->getPathname();
				$subpathname = trim($children->getSubPathname(), '/');

				if ($children->hasChildren() === true) {
					if ($preserve_keys == true) {
						$structure[] = $pathname;
					}

					$sub_structure = get_directory_structure_flatten($pathname, $preserve_keys);
					$structure = array_merge($structure, $sub_structure);
				} else {
					$structure[] = $pathname;
				}

				$children->next();
			}
		}

		ksort($structure);

		return $structure;
	}
}

if (function_exists('hasFullAccess') === false)
{
	function hasFullAccess()
	{
		if (isset($_SERVER['REMOTE_ADDR']) === false) {
			return false;
		}

		if (class_exists('Auth') === false) {
			return false;
		} elseif (Auth::check() === true) {
			if (in_array(Auth::user()->email, config('admin')) === true) {
				return true;
			}
		}

		return false;
	}
}

if (function_exists('recursive_array_search') === false)
{
	function recursive_array_search($needle, $haystack)
	{
		foreach ($haystack as $key => $value) {
			$current_key = $key;

			if ($needle === $value || (is_array($value) && recursive_array_search($needle, $value) !== false)) {
				return $current_key;
			}
		}
		return false;
	}
}

if (function_exists('recursive_directory_remove') === false)
{
	function recursive_directory_remove($full_path)
	{
		if (file_exists($full_path) === false) {
			@unlink($full_path);
			return false;
		}

		$filters = RecursiveIteratorIterator::CHILD_FIRST;
		$dir_path_iterator = new RecursiveDirectoryIterator($full_path, FilesystemIterator::SKIP_DOTS);

		foreach(new RecursiveIteratorIterator($dir_path_iterator, $filters) as $path) {
			$path->isFile() ? unlink($path->getPathname()) : recursive_directory_remove($path->getPathname());
		}

		rmdir($full_path);
	}
}

if (function_exists('search_file') === false)
{
	function search_file($directory, $filename)
	{
		$directory = new RecursiveDirectoryIterator(
			$directory,
			RecursiveDirectoryIterator::KEY_AS_FILENAME |
			RecursiveDirectoryIterator::CURRENT_AS_FILEINFO
		);

		$files = new RegexIterator(
			new RecursiveIteratorIterator($directory),
			'#^'.preg_quote($filename).'$#',
			RegexIterator::MATCH,
			RegexIterator::USE_KEY
		);

		$files_list = [];

		foreach ($files as $file) {
			array_push($files_list, $file->getPathname());
		}

		return $files_list;
	}
}
