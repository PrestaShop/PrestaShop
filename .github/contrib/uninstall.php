<?php

if (file_exists('.git/hooks/pre-commit')) {
    copy('.git/hooks/pre-commit', '.git/hooks/pre-commit.back');
    unlink('.git/hooks/pre-commit');
    echo "Pre-commit Hook has been removed.\n";
}
