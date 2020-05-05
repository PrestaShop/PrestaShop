#!/bin/bash

echo "Install node js v10 for puppeteer v3";

sudo rm -rf ~/.nvm
curl -sL https://deb.nodesource.com/setup_10.x | sudo -E bash -
sudo apt-get install -y nodejs
