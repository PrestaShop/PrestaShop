#!/bin/sh

#REMOVE PHP-CS-FIXER
if [ -f php-cs-fixer ]
  then
    rm php-cs-fixer
fi

#REMOVE GIT PRE-COMMIT
if [ -f .git/hooks/pre-commit ]
  then
    cp .git/hooks/pre-commit .git/hooks/pre-commit.back
    rm .git/hooks/pre-commit
fi

echo "Pre-commit Hook has been removed."

exit $?