<?php

namespace Wikimedia\Deployments\ToDeploy\Utils;

class OSUtils {

    /**
     * Gets the real path of a command on the current environment
     *
     * @param string $command The command to get
     * @return string The real full path for the command
     * @throws RuntimeException when the command isn't found
     */
    static function getCommandRealPath ($command) {
      $whereIsCommand = (PHP_OS == 'WINNT') ? 'where' : 'command -v';

      $process = proc_open(
        "$whereIsCommand $command",
        array(
          0 => array("pipe", "r"), //STDIN
          1 => array("pipe", "w"), //STDOUT
          2 => array("pipe", "w"), //STDERR
        ),
        $pipes
      );
      if ($process !== false) {
        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        proc_close($process);

        $result = trim($stdout);
        if ($result !== '') {
            return $result;
        }
      }

      throw new \RuntimeException("Command not found");
    }
}
