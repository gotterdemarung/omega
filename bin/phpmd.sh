#!/bin/bash

# Cd to script folder
cd "$(dirname "$0")"

../vendor/bin/phpmd ../src/ text codesize,unusedcode,naming