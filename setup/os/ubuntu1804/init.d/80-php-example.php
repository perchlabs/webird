#!/usr/bin/env php
<?php
// Environment variables OS_DIR and TEMP_DIR are available

$COLOR_SECTION=str_replace('\e', "\e", getenv('COLOR_SECTION'));
$TEXT_RESET=str_replace('\e', "\e", getenv('TEXT_RESET'));

echo "${COLOR_SECTION}*** PHP Script Example ***${TEXT_RESET}\n";
echo "You can use scripts other than Bash\n";

exit(0);
