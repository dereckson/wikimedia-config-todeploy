#!/usr/bin/env php
<?php

require __DIR__ . '/../vendor/autoload.php';

// Configuration

$config = [
    'PhabricatorURL' => "https://phabricator.wikimedia.org",
    'PhabricatorBoardId' => 178, //Wikimedia-Site-requests Workboard

    'GerritServer' => 'gerrit.wikimedia.org',
    'GerritProject' => "operations/mediawiki-config",

    'Tag' => 'config'
];

// Run tasks

Wikimedia\Deployments\ToDeploy\Task::run($config);
