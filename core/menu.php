<?php

global $menu, $loggedin;

$menu = array();
$menu[] =  array(
    'title' => 'Home',
    'url' => './',
    'icon' => 'home',
);
if ($loggedin) {
    $menu[] =  array(
        'title' => 'Profile',
        'url' => './',
        'icon' => 'user',
    );
}
$menu[] =  array(
    'title' => 'Categories',
    'url' => './categories',
    'icon' => 'sitemap',
);
$menu[] =  array(
    'title' => 'Music player',
    'url' => './music',
    'icon' => 'music',
);
$menu[] =  array(
    'title' => 'Channels',
    'url' => './channels',
    'icon' => 'video-camera',
);
$menu[] =  array(
    'title' => 'Suggest video or channel',
    'url' => './suggest',
    'icon' => 'comment',
);
$menu[] =  array(
    'title' => 'About and updates',
    'url' => './about',
    'icon' => 'info-circle',
);
$menu[] =  array(
    'title' => 'Contact',
    'url' => './contact',
    'icon' => 'envelope',
);
if ($loggedin) {
    $menu[] =  array(
        'title' => 'Log out',
        'url' => './logout',
        'icon' => 'power-off',
    );
} else {
    $menu[] =  array(
        'title' => 'Register',
        'url' => './register',
        'icon' => 'users',
    );
    $menu[] =  array(
        'title' => 'Log in',
        'url' => './login',
        'icon' => 'user',
    );
}