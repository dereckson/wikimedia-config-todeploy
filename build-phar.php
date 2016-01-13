<?php

class PharBuilder {
    ///
    /// Properties
    ///

    public $settings;
    private $phar;

    ///
    /// Static application entry point
    ///

    /**
     * The main software entry point
     */
    public static function run ($argc, $argv) {
        //Handles arguments

        if ($argc != 2) {
            echo "Usage: $argv[0] <settings JSON file>\n";
            exit(2);
        }

        $settingsFile = $argv[1];
        if (!file_exists($settingsFile)) {
            echo "File not found: $settingsFile\n";
            exit(4);
        }

        //Calls builder instance
        try {
            $builder = new static($settingsFile);
            $builder->build();
        } catch (Exception $ex) {
            echo $ex->getMessage(), "\n";
            exit(8);
        }
    }

    ///
    /// Constructor
    ///

    public function __construct ($settingsFile) {
        $this->settings = static::getSettings($settingsFile);
    }

    ///
    /// Core methods
    ///

    /**
     * Checks the settings and launch the PHAR archive build
     */
    public function build()  {
        $this->validateSettings();
        $this->createPhar();
    }

    public function createPhar () {
        $options = FilesystemIterator::CURRENT_AS_FILEINFO
                 | FilesystemIterator::KEY_AS_FILENAME;
        $name = $this->getName();
        $this->phar = new Phar($this->settings->target, $options, $name);
        $this->addFiles();
        $this->createStub();
    }

    public function createStub () {
        $stubFile = $this->getStubFile();
        $stub = $this->phar->createDefaultStub($stubFile);
        $shebang = "#!/usr/bin/env php\n";
        $this->phar->setStub($shebang . $stub);
    }

    public function getStubFile () {
        if (property_exists($this->settings, 'entryPoint')) {
            $candidate = $this->settings->entryPoint;
        } else {
            $candidate = 'index.php';
        }
        if (!$this->phar->offsetExists($candidate)) {
            throw new Exception("Can't determine entry point as $candidate doesn't exist. Check your entryPoint parameter.");
        }
        return $candidate;
    }

    public function addFiles () {
        $source = $this->settings->source;
        $dh = opendir($source);
        while (($file = readdir($dh)) !== false) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            $path = $source . DIRECTORY_SEPARATOR . $file;
            $type = filetype($path);
            if ($type == "file") {
                $code = static::cleanCode(file_get_contents($path));
                $this->phar[$file] = $code;
            }
            //TODO: what behavior for symlinks ('link') and subdirs ('dir')?
        }
        closedir($dh);
    }

    /**
     * Cleans code
     *
     * @param string $code source code to clean
     * @return string cleaned up code
     */
     public static function cleanCode ($code) {
         if (substr($code, 0, 2) == '#!') {
             // Ignores shebang line
             $pos = strpos($code, "\n");
             return substr($code, 19);
         }
         return $code;
     }

    /**
     * Gets settings from a JSON file
     *
     * @param string $file the JSON settings file
     * @return stdClass an object with the source and target properties
     */
    public static function getSettings ($file) {
        $data = file_get_contents($file);
        return json_decode($data);
    }

    ///
    /// Validation methods
    ///

    public function validateSettings () {
        if ($this->settings === null) {
            throw new InvalidArgumentException("Settings are not defined.");
        }

        if (!is_object($this->settings)) {
            throw new InvalidArgumentException("Settings is expected to be an object.");
        }

        $this->validateProperties();
    }

    public function validateProperties () {
        $expectedProperties = ['source', 'target'];

        foreach ($expectedProperties as $property) {
            if (!property_exists($this->settings, $property)) {
                throw new InvalidArgumentException("Required parameter missing: $property");
            }
            $validatePropertyMethod = 'validateParameter' . ucfirst($property);
            $this->$validatePropertyMethod();
        }
    }

    public function validateParameterSource () {
        $source = $this->settings->source;
        if (!file_exists($source) || !is_dir($source)) {
            throw new InvalidArgumentException("Source directory $source doesn't exist.");
        }
    }

    public function validateParameterTarget () {
        $target = $this->settings->target;
        if (file_exists($target) && is_dir($target)) {
            throw new InvalidArgumentException("Target must be the path to the .phar file, not a directory.");
        }

        /*
        $dir = $this->getDirectory($target);
        if (!is_writable($dir)) {
            throw new RuntimeException("Directory $dir isn't writable.");
        }
        */
    }

    ///
    /// Helper methods
    ///

    public function getName () {
        return basename($this->settings->target) . '.phar';
    }

}

PharBuilder::Run($argc, $argv);
