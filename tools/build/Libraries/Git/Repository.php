<?php

class Repository
{
    /**
     * @param string $orga
     * @param string $repo
     * @param string $destination
     * @return bool
     */
	public static function cloneRepository($orga, $repo, $destination = '')
	{
		// Remove old repository.
		if ((empty($destination) && file_exists($repo))
            || ($destination != '' && file_exists($destination) == true)
        ) {
			recursive_directory_remove($destination);
		}
		exec("git clone https://github.com/$orga/$repo.git $destination");
	}

}
