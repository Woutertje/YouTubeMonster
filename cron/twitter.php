<?php

$t = strtotime('now') + microtime();
include('../core/config.php');
$GLOBALS['config']['root'] = '../';
include('../core/startup.php');

onlyAdmin();

function debug($str){
    echo $str.'<br />';
}

debug('Twitter testing.');

# Twitter setup
include('../classes/OAuth.php');
include('../classes/twitter.php');

global $config;

$twitter = new Twitter(
    $config['twitter']['consumerKey'],
    $config['twitter']['consumerSecret'],
    $config['twitter']['accessToken'],
    $config['twitter']['accessTokenSecret']
);
if(!$twitter->authenticate())
    die('Invalid twitter name or password');
debug('Twitter account connected.');

# Spam on twitter
try{
    $twitter->send('YouTubeMonster is now #OpenSource on #GitHub! Check it out: https://github.com/FuturePortal/YouTubeMonster');
    debug('Spammed on twitter!');
}
catch(TwitterException $e){
    debug('<span style="color: #D00000;">Did not post on twitter: '.$e->getMessage().'</span>');
}

# Ending.
debug('');
debug('Ended ('.number_format((((strtotime('now') + microtime()) - $t) * 1000), 2, '.', '').' ms).');