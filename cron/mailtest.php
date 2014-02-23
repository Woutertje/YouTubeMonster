<?php

# Globals
global $config;

# setup
include('../core/config.php');
$config['root'] = '../';
include('../core/startup.php');

# Make sure only admins can run this file
onlyAdmin();

# Reset todays ratings
$mail = new templatemail('just testing', 'test', array(
    'email' => 'user'
));
if($mail->send())
    echo 'Mail sent.';
else
    echo 'Mail not sent.';