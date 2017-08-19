<?php

namespace App\Services\LameDb;

/**
 * Main class to manipulate lamedb files
 *
 * @throws LameDbException
 */
abstract class LameDb
{
    /**
     * lamedb version to use if not specified
     */
    const DEFAULT_VERSION = 4;

    /**
     * lamedb to use if not specified
     * @var int
     */
    protected $_versionAccepted = array(4);

    /**
     * Version of loaded lamedb file
     * @var int
     */
    protected $_version;

    /**
     * @var Transponder[]
     */
    protected $_transponders = array();

    /**
     * @var Service[]
     */
    protected $_services = array();

    /**
     * @var array
     */
    protected $_mapName = array();

    /**
     * Create
     *
     * @static
     * @throws LameDbException
     * @param int $version
     * @return LameDb
     */
    static function factory($version = null)
    {
        if (is_null($version)) {
            $version = self::DEFAULT_VERSION;
        }
        switch ($version)
        {
            case 4:
                return new LameDb4($version);
            default:
                throw new LameDbException("lamedb version '$version' is not unsupported.");
        }
    }

    /**
     * Load existing lamedb file and return appropriate lamedb class
     *
     * @static
     * @param  string|stream $source
     * @return LameDb
     * @throws LameDbException
     */
    static function factoryFromFile($source)
    {
        if (is_string($source)) {
            $source = fopen($source, "r");
        }
        // check header and obtain version info
        if (false !== ($s = trim(fgets($source)))) {
            $version = self::parseVersion($s);
            if (false === $version) {
                throw new LameDbException("wrong header '$s'");
            }
        } else {
            throw new LameDbException("lamedb file is empty");
        }
        // factory the correct class
        $lamedb = self::factory($version);
        $lamedb->load($source, false);
        return $lamedb;
    }

    /**
     * @return Service[]
     */
    public function getServices()
    {
        return $this->_services;
    }

    /**
     * Parse first line
     *
     * This method should never be overridden by subclasses, should be fixed here instead.
     *
     * @static
     * @param  string $line
     * @return int FALSE if problem
     */
    final static function parseVersion($line)
    {
        $version = array();
        if (!preg_match("@eDVB services /(\d)/@", $line, $version)) {
            return false;
        }
        return $version[1];
    }

    /**
     * Load content of lamedb file
     *
     * @abstract
     * @param  string|stream $source
     * @param  bool $checkVersion
     * @return void
     * @throws LameDbException
     */
    function load($source, $checkVersion = true)
    {
        if (is_string($source)) {
            $source = fopen($source, "r");
            // make sure version will be checked
            $checkVersion = true;
        }
        if ($checkVersion) {
            if (false !== ($s = trim($this->_fgets($source)))) {
                $version = self::parseVersion($s);
                if (false === $version) {
                    throw new LameDbException("wrong header '$s'");
                }
                if (in_array($version, $this->_versionAccepted)) {
                    throw new LameDbException("you can't load lamedb version '$version' with class " . get_class($this));
                }
            } else {
                throw new LameDbException("lamedb file is empty");
            }
        }

        // load transponders
        $this->_loadTransponders($source);
        // load services
        $this->_loadServices($source);
    }

    public function exportToTxt($fn)
    {
        $f = fopen($fn, "w");
        foreach ($this->_services as $k => $service) {
            $t = $this->getTransponder($service->getTransponderKey());
            //var_dump($t);die;
            $data = array(
                //$service->networkId,
                $service->packageName,
                $service->name,
                $service->sid,
                implode(",", array($t->getSatelliteName(), $t->frequency, $t->getPolarizationCode(), $t->getFecStr()))
            );
            fputs($f, implode(";", $data)."\n");
        }
        fclose($f);
    }

    /**
     * @param string $packageName
     * @param string $serviceName
     * @return string[]
     */
    public function getKeyByPackageServiceName($packageName, $serviceName)
    {
        $packageName = mb_strtoupper($packageName);
        $serviceName = mb_strtoupper($serviceName);
        if (array_key_exists($serviceName, $this->_mapName)) {
            // found based on name
            $a1 = $this->_mapName[$serviceName];
            if (count($a1)==1) {
                // there is only 1 package
                return current($a1);
            } else {
                // check also package name
                if (array_key_exists($packageName, $a1)) {
                    return $a1[$packageName];
                } else {
                    // package not found, using first package found
                    return current($a1);
                }
            }
        }
        // not found
        echo "<b>$serviceName/$packageName:</b> ".$this->getSimilar($serviceName).'<br />';
        return false;
    }

    public function getSimilar($serviceName)
    {
        $res = array();
        $sound = soundex($serviceName);
        foreach ($this->_mapName as $serviceName=>$a) {
            if ($sound==soundex($serviceName)) {
                foreach ($a as $packageName=>$b)
                    $res[] = "$packageName/$serviceName";
            }
        }
        return implode(",", $res);
    }

    public function getKeyByFrequency($freq)
    {
        // TODO not clear now
        $freq = strtoupper(trim($freq));
        foreach ($this->_services as $key=>$service) {
            $t = $this->getTransponder($service->getTransponderKey());
            $fkey = implode(",", array($t->getSatelliteName(), $t->frequency, $t->getPolarizationCode(), $t->getFecStr()));
            if ($fkey==$freq) {
                return $key;
            }
        }


        return false;
    }

    public function getService($key)
    {
        if (array_key_exists($key, $this->_services)) {
            return $this->_services[$key];
        } else {
            return false;
        }
    }

    /**
     * @param string $key
     * @return Transponder
     */
    public function getTransponder($key)
    {
        if (array_key_exists($key, $this->_transponders)) {
            return $this->_transponders[$key];
        } else {
            return false;
        }
    }

    /**
     * Default constructor
     *
     * @param int $version
     */
    final public function __construct($version)
    {
        $this->_version = $version;
    }

    protected function _loadTransponders($source)
    {
        // find begin of transponders
        while (false !== ($s = trim($this->_fgets($source)))) {
            if ($s == "transponders") {
                break;
            }
        }
        // read transponders
        while (false !== ($l1 = trim($this->_fgets($source)))) {
            if ($l1 == "end") {
                break;
            }
            // TODO maybe we need to loop until '/' found
            $data = trim($this->_fgets($source));
            $slash = trim($this->_fgets($source));
            if ($slash != '/') {
                throw new LameDbException("transponder definition does not end with '/''");
            }

            // parse and store transponder
            $transponder = $this->_createTransponder(array($l1, $data));
            $this->_transponders[$transponder->getKey()] = $transponder;
        }
    }

    protected function _fgets($source)
    {
        $s = fgets($source);
        // TODO non-breaking space, this should be improved
        $s = str_replace(chr(194).chr(134), '', $s);
        $s = str_replace(chr(194).chr(135), '', $s);
        return $s;
    }

    protected function _loadServices($source)
    {
        // find begin of service definition
        while (false !== ($s = trim($this->_fgets($source)))) {
            if ($s == "services") {
                break;
            }
        }
        // read services
        while (false !== ($l1 = trim($this->_fgets($source)))) {
            if ($l1 == "end") {
                break;
            }
            $serviceName = trim($this->_fgets($source));

            //echo $serviceName.": ".mb_detect_encoding($serviceName)."<br/>";

            $l3 = $this->_fgets($source);

            $service = $this->_createService(array($l1, $serviceName, $l3));

            // store service
            $key = $service->getKey();
            if (!array_key_exists($key, $this->_services)) {
                $this->_services[$key] = $service;
            }

            // store mapping to judge service by name
            $name = $service->name;
            $packageName = $service->packageName;
            $name = strtoupper($name);
            $packageName = strtoupper($packageName);
            if (array_key_exists($name, $this->_mapName)) {
                if (array_key_exists($packageName, $this->_mapName[$name])) {
                    $this->_mapName[$name][$packageName][] = $key;
                } else {
                    $this->_mapName[$name][$packageName] = array($key);
                }
            } else {
                $this->_mapName[$name] = array($packageName=>array($key));
            }
        }
    }

    /**
     * @abstract
     * @param  array $data
     * @return Transponder
     */
    abstract protected function _createTransponder($data);

    /**
     * @abstract
     * @param  array $data
     * @return Service
     */
    abstract protected function _createService($data);
}
