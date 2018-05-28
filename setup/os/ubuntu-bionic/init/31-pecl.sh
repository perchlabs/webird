#!/usr/bin/env bash
# Environment variables OS_DIR and TEMP_DIR are available

[[ ! -z "${SKIP_PECL+x}" ]] && exit 0

list=$(readlist pecl)
for extension in $list
do
  php-pecl-install $extension
  [[ $? -ne 0 ]] && exit 1
done

# Install PECL mailparse extension with patch to work around PHP 7 issue
# Fix for issue: https://bugs.php.net/bug.php?id=71813
mkdir -p "$TEMP_DIR/pecl"
cd "$TEMP_DIR/pecl"
MAILPARSE=mailparse-3.0.1
wget https://pecl.php.net/get/$MAILPARSE.tgz
[[ $? -ne 0 ]] && exit $?
tar -xf $MAILPARSE.tgz
cd $MAILPARSE
# Patch the source to fix mbstring issue
patch << EOL
--- mailparse_orig.c    2016-09-15 19:15:03.705843654 -0700
+++ mailparse.c 2016-09-15 19:15:19.273841017 -0700
@@ -30,9 +30,9 @@
 #include "php_open_temporary_file.h"

 /* just in case the config check doesn't enable mbstring automatically */
-#if !HAVE_MBSTRING
-#error The mailparse extension requires the mbstring extension!
-#endif
+// #if !HAVE_MBSTRING
+// #error The mailparse extension requires the mbstring extension!
+// #endif

 #define MAILPARSE_DECODE_NONE          0               /* include headers and leave section untouched */
 #define MAILPARSE_DECODE_8BIT          1               /* decode body into 8-bit */
EOL

phpize
./configure
make
make install
php-extension-enable mailparse

exit 0
