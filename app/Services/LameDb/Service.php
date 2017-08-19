<?php

namespace App\Services\LameDb;

class Service
{
    public $myName;
    public $name;
    public $packageName;
    public $sid;
    public $namespace;
    public $transporterId;
    public $networkId;
    public $serviceType; // 1: TV, 2: Radio, 25: HDTV, other:data
    public $hmm2;

    public function getKey()
    {
        // TODO normalize namespace and sid
        return strtoupper($this->namespace . '#' . $this->sid . '#' . $this->transporterId);
        //return $this->packageName . "#" . $this->name;
    }

    public function getTransponderKey()
    {
//        var_dump($this);
        return strtoupper($this->namespace . ':' . $this->transporterId . ':' . $this->networkId);
    }
}
