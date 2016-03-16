<?php

namespace Wikimedia\Deployments\ToDeploy\Gerrit;

class Gerrit {
    const DEFAULT_PORT = 29418;
    const DEFAULT_HOST = 'localhost';

    public $host;
    public $port;

    function __construct ($host = self::DEFAULT_HOST, $port = self::DEFAULT_PORT) {
        $this->host = $host;
        $this->port = $port;
    }

    function listProjects () {
        return $this->exec('ls-projects');
    }

    function exec ($command) {
        return `ssh -p $this->port $this->host gerrit $command`;
    }

    function query ($query) {
        $reply = `ssh -p $this->port $this->host gerrit query --format json "$query"`;
        $lines = explode(PHP_EOL, $reply);
        $results = [];
        foreach ($lines as $line) {
            $results[] = json_decode($line);
        }
        return $results;
    }

    public static function getTrackingId ($trackingIds, $system) {
        foreach ($trackingIds as $trackingId) {
            if ($trackingId->system == $system) {
                return $trackingId->id;
            }
        }
        return null;
    }
}
