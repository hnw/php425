#!/bin/sh
PROJECT_BIN_DIR=$(cd $(dirname $0); pwd)
PROJECT_DIR=$(dirname $PROJECT_BIN_DIR)
PROJECT_VENDOR_DIR=$PROJECT_DIR/vendor

PEAR=$PROJECT_BIN_DIR/pear
PEAR_CONF=$PROJECT_DIR/etc/pear.conf
PEAR_DIR=$PROJECT_VENDOR_DIR/pear
PEAR_TMP_DIR=$PROJECT_DIR/tmp/pear

$PEAR config-create $PROJECT_VENDOR_DIR $PEAR_CONF
$PEAR -c $PEAR_CONF config-set bin_dir      $PEAR_DIR/bin
$PEAR -c $PEAR_CONF config-set cache_dir    $PEAR_TMP_DIR/cache
$PEAR -c $PEAR_CONF config-set download_dir $PEAR_TMP_DIR/download
$PEAR -c $PEAR_CONF config-set ext_dir      `php-config --extension-dir`
$PEAR -c $PEAR_CONF config-set temp_dir     $PEAR_TMP_DIR/temp
