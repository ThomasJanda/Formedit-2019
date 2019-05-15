<?php

$this->url="http://www.reisacher-software.de/formedit2";



/**
 * file open, file save dialog
 */
$this->fileBrowser = [
    'editor' => [
        'path' => "./projectfiles",
        'url' => $this->url."/projectfiles",
        'ext' => [
            'cpf2'
        ]
    ],
    'assetimage' => [
        'path' => "./asset",
        'url' => $this->url."/asset",
        'ext' => [
            'png',
            'jpg'
        ]
    ],
    'assetlink' => [
        'path' => "./asset",
        'url' => $this->url."/asset",
        'ext' => []
    ],
];


/**
 *  project settings
 */
//where can the project connect to
$this->projectConnection = [
    'mysql' => [
        'name' => 'MySQL',
        'class' => 'formedit\addons\connections\mysql',
        'properties' => [
            /** properties which use in the class */
            'host' => 'mysql5.reisacher-software.de',
            'port' => 3306,
            'schema' => 'db26140_94',
            'user' => 'db26140_94',
            'pass' => 'zQKDtq4Kw7',
            'charset' => 'utf8',
        ]
    ],
    /*
    'sqllite' => [
        'name' => 'Sql Lite',
        'class' => 'addons\connections\sqllite',
        'properties' => [
            'path' => 'out/sqllite'
        ]
    ],
    'laravel' => [
        'name' => 'Laravel',
        'class' => 'addons\connections\laravel',
        'properties' => [
        ]
    ],
    */
];