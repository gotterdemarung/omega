#!/bin/bash

# Cd to script folder
cd "$(dirname "$0")"

# Cd to tests folder
cd ../test/

../vendor/bin/phpunit unit