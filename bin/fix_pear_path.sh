#!/bin/sh
PROJECT_BIN_DIR=$(cd $(dirname $0); pwd)
PROJECT_DIR=$(dirname $PROJECT_BIN_DIR)
PROJECT_PEAR_DIR=$PROJECT_DIR/vendor/pear

PHP_PEAR_INSTALL_DIR=$PROJECT_PEAR_DIR/php

sed -i "s!${PHP_PEAR_INSTALL_DIR}!@php_dir@!g" \
  ${PROJECT_PEAR_DIR}/bin/{pear,peardev,pecl}
sed -i "s!${PHP_PEAR_INSTALL_DIR}!@include_path@!g" \
  ${PROJECT_PEAR_DIR}/php/{pearcmd.php,peclcmd.php}
