command -v grunt >/dev/null 2>&1 || { echo "I require grunt-cli to be installed globally. Sudo needed for that (or abort and install it manually)."; sudo npm install -g grunt-cli; exit 0; }
