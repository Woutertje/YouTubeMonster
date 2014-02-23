<?php

# setup
$t = strtotime('now') + microtime();
include('../core/config.php');
$GLOBALS['config']['root'] = '../';
include('../core/startup.php');

# Make sure only admins can run this file
onlyAdmin();

# Globals
global $db;

# Reset todays ratings
$db->query('
    UPDATE `videos`
    SET `scoretoday` = 0
');
$db->query('
    TRUNCATE TABLE `rates`
');

# Finish
echo 'Done.';