#!/bin/bash

echo "Install composer v1.10.16";

 curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer --version=1.10.16
