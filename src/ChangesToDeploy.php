<?php

require 'GerritQueryResult.php';

class ChangesToDeploy {
    private $gerrit;
    private $project;

    public function __construct (Gerrit $gerrit, $project) {
        $this->gerrit = $gerrit;
        $this->project = $project;
    }

    public function fetch () {
        $changes = $this->getCandidatesChanges();
        return new GerritQueryResult($changes);
    }

    private function getCandidatesChanges () {
        $query = "status:open project:$this->project";
        return $this->gerrit->query($query);
    }

    public static function format (Array $changes, $prefix = '') {
        $lines = [];
        foreach ($changes as $change) {
            if (!is_object($change) || !property_exists($change, 'number')) {
                continue;
            }
            $line  = "* [$prefix] {{Gerrit|$change->number}} ";
            $line .= $change->subject;
            if ($change->trackingIds) {
                $task = Gerrit::getTrackingId(
                    $change->trackingIds,
                    'Bugzilla'
                );
                if ($task !== null) {
                    $line .= " (T$task)";
                }
            }
            $lines[] = $line;
        }
        return join(PHP_EOL, $lines);
    }
}
