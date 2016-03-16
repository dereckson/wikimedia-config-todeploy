<?php

namespace Wikimedia\Deployments\ToDeploy;

class GerritQueryResult {
    private $changes;

    public function __construct ($changes) {
        $this->changes = $changes;
    }

    public function filterByTasksId ($tasksId) {
        $filteredChanges = [];

        foreach ($this->changes as $change) {
            if (!is_object($change) || !property_exists($change, 'trackingIds')) {
                continue;
            }
            $taskId = Gerrit::getTrackingId($change->trackingIds, 'Bugzilla');
            if ($taskId === null) {
                continue;
            }
            if (in_array($taskId, $tasksId)) {
                $filteredChanges[] = $change;
            }
        }

        $this->changes = $filteredChanges;
        return $this;
    }

    public function get () {
        return $this->changes;
    }
}
