<?php

namespace App\Services\LameDb;

class Transponder
{
    public $namespace;
    public $networkId;
    public $transporterId;
    public $frequency;
    public $symbol_rate;
    public $polarization;
    public $fec;
    public $position;
    public $inversion;
    public $flags;
    public $system;
    public $modulation;
    public $rolloff;
    public $pilot;
    public $unknown1;
    public $unknown2;
    public $unknown3;

    protected $_mapPolarization = array(
        0=>array('H', 'Horizontal'),
        1=>array('V', 'Vertical'),
        2=>array('L', 'Circular Left'),
        3=>array('R', 'Circular Right'),
    );
    protected $_mapFEC = array(
        0=>'Auto',
        1=>'1/2',
        2=>'2/3',
        3=>'3/4',
        4=>'5/6',
        5=>'7/8',
        6=>'8/9',
        7=>'3/5',
        8=>'4/5',
        9=>'9/10'
    );

    /**
     * Return unique key for transponder
     * @return string
     */
    public function getKey()
    {
        return strtoupper($this->namespace . ':' . $this->transporterId . ':' . $this->networkId);
    }

    public function getPolarizationCode()
    {
        return $this->_mapPolarization[$this->polarization][0];
    }
    public function getPolarizationStr()
    {
        return $this->_mapPolarization[$this->polarization][1];
    }
    public function getFecStr()
    {
        return $this->_mapFEC[$this->fec];
    }
    public function getSatelliteName()
    {
        // TODO we need to support loading of satellites.xml
        return $this->position;
    }
}
