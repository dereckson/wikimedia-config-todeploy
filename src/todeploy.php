#!/usr/bin/env php
<?php

require 'vendor/autoload.php';

require 'ChangesToDeploy.php';
require 'Gerrit.php';
require 'PhabricatorBoardScraper.php';

// Configuration

$config = [
    'PhabricatorURL' => "https://phabricator.wikimedia.org",
    'PhabricatorBoardId' => 178, //Wikimedia-Site-requests Workboard

    'GerritServer' => 'gerrit.wikimedia.org',
    'GerritProject' => "operations/mediawiki-config",

    'Tag' => 'config'
];

// Scrapes Phabricator project board to gets the tasks to deploy

$board = new PhabricatorBoardScraper(
    $config['PhabricatorURL'],
    $config['PhabricatorBoardId']
);
if (array_key_exists('PhabricatorBoardColumn', $config)) {
    $board->columnName = $config['PhabricatorBoardColumn'];
}
$boardTasks = $board->GetTasksId();

// Queries Gerrit

$gerrit = new Gerrit($config['GerritServer']);
$changes = (new ChangesToDeploy($gerrit, $config['GerritProject']))
    ->fetch()
    ->filterByTasksId($boardTasks)
    ->get();

if (count($changes)) {
    echo ChangesToDeploy::format($changes, $config['Tag']);
    echo "\n";
}
