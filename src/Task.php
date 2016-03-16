<?php

namespace Wikimedia\Deployments\ToDeploy;

use Wikimedia\Deployments\ToDeploy\Gerrit\Gerrit;
use Wikimedia\Deployments\ToDeploy\Phabricator\PhabricatorBoardScraper;

class Task {

    /**
     * Runs the task
     */
    public static function run ($config) {
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
    }

}
