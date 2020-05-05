#!/bin/bash

echo "Install node js";

sudo rm -rf ~/.nvm - curl -sL https://deb.nodesource.com/setup_10.x | sudo -E bash -
sudo apt-get install -y nodejs
