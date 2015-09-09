#!/bin/sh

#INSTALL GIT PRE-COMMIT
if [ -f .git/hooks/pre-commit ]
  then
    cp .git/hooks/pre-commit .git/hooks/pre-commit.back
    echo -e "\033[0;31mA Git precommit file was found, a backup was created.\e[0;m"
fi

cp tools/contrib/pre-commit .git/hooks/pre-commit
chmod +x .git/hooks/pre-commit
echo "Pre-commit Hook has been installed."
