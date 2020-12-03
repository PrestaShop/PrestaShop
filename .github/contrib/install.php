<?php

if (file_exists('.git/hooks/pre-commit')) {
    copy('.git/hooks/pre-commit', '.git/hooks/pre-commit.back');
    echo "A Git precommit file was found, a backup was created.\n";
}

file_put_contents(
    '.git/hooks/pre-commit',
    file_get_contents(__DIR__.'/pre-commit')
);

chmod('.git/hooks/pre-commit', 0750);
echo "\e[32mPre-commit Hook has been installed.\e[0;m\n";
