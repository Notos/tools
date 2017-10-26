<?php

namespace App\Services\LameDb;

use Transliterator;

class LameDb4 extends LameDb
{
    /**
     * lamedb to use if not specified
     * @var int
     */
    protected $_versionsAccepted = array(4);

    protected function _createTransponder($data)
    {
        list($line1, $line2) = $data;

        $t = new Transponder();

        list($t->namespace, $t->transporterId, $t->networkId) = explode(":", $line1);

        $line2 = trim($line2);

        switch ($line2[0]) {
            case 'c':
                $a = explode(":", trim(substr($line2,1)));
                if (count($a)>11) {
                    throw new LameDbException("too many parameters in transponder data '$line2'");
                }
                @list(
                    $t->frequency,
                    $t->symbol_rate,
                    $t->polarization,
                    $t->fec,
                    $t->position,
                    $t->inversion,
                    $t->flags,
                    $t->system,
                    $t->modulation,
                    $t->rolloff,
                    $t->pilot
                    ) = $a;
                break;
            case 's':
                $a = explode(":", trim(substr($line2,1)));
                if (count($a) == 11) {
                    $a[] = 'ignored';
                    $a[] = 'ignored';
                    $a[] = 'ignored';
                }
                if (count($a)>14) {
                    throw new LameDbException("too many parameters in transponder data '$line2'");
                }
                @list(
                    $t->frequency,
                    $t->symbol_rate,
                    $t->polarization,
                    $t->fec,
                    $t->position,
                    $t->inversion,
                    $t->flags,
                    $t->system,
                    $t->modulation,
                    $t->rolloff,
                    $t->pilot,
                    $t->unknown1,
                    $t->unknown2,
                    $t->unknown3
                    ) = $a;
                break;
            default:
                throw new LameDbException("unknown transponder data line '$line2'");
        }

        return $t;
    }

    protected function _createService($data)
    {
        // get parameters
        list($l1, $serviceName, $l3) = $data;

        // check hex values http://www.mathsisfun.com/binary-decimal-hexadecimal-converter.html
        $packageName = "no-package";
        $l1 = explode(":", trim($l1));
        $l3 = explode(",", trim($l3));
        // TODO don't know about cacheIDs
        foreach ($l3 as &$v) {
            if ($v[0] == "p" && $v[1] == ":") {
                $a = explode(",", $v);
                $packageName = trim(substr($a[0], 2));
                if (!$packageName) {
                    $packageName = "no-provider";
                }
                $v = null;
            }
        }

        $service = new Service();
        $service->name = $serviceName;
        $service->packageName = $packageName;
        $service->sid = $l1[0];
        $service->lsid = ltrim($service->sid, '0');
        $service->namespace = $l1[1];
        $service->transporterId = $l1[2];
        $service->ltransporterId = ltrim($service->transporterId, '0');
        $service->networkId = $l1[3];
        $service->lnetworkId = ltrim($service->networkId, "0");
        $service->serviceType = $l1[4];
        $service->hmm2 = $l1[5];
        $service->hmm3 = $l1[6];
        $service->l1 = $l1;
        $service->l3 = $l3;
        $service->data = $data;
        $service->channelId = strtoupper("1:0:1:{$service->lsid}:{$service->ltransporterId}:{$service->lnetworkId}:{$service->namespace}:0:0:0");

        $service->filenames = [
            str_replace(' ', '', 'canal'.strtolower($name1 = $this->normalizeName($service->name, true))).'.png' => str_replace(' ', '', 'canal'.strtolower($name1 = $this->normalizeName($service->name, true))).'.png',
            str_replace(' ', '', 'canal'.strtolower($name2 = $this->normalizeName($service->name, false))).'.png' => str_replace(' ', '', 'canal'.strtolower($name2 = $this->normalizeName($service->name, false))).'.png',
        ];

        $service->enigma2Filenames = [
            str_replace(':', '_', $service->channelId).'.png' => strtoupper("1_0_1_{$service->lsid}_{$service->ltransporterId}_{$service->lnetworkId}_{$service->namespace}_0_0_0").'.png',
        ];

        $service->shieldFilenames = [
            str_replace(' ', '_', 'canal'.strtolower($name1)).'.png' => str_replace(' ', '_', 'canal'.strtolower($name1)).'.png',
            str_replace(' ', '_', 'canal'.strtolower($name2)).'.png' => str_replace(' ', '_', 'canal'.strtolower($name2)).'.png',
        ];

        return $service;
    }

    private function normalizeName($name, $changeExceptionToNames)
    {
        return strtr(
            $this->unaccent($name),
            [
                '-' => ' ',
                '_' => ' ',
                '/' => '',
                '\\' => '',
                '\'' => '',
                '"' => '',
                '`' => '',
                '?' => '',
                '(' => '',
                ')' => '',
                ':' => '',
                '<' => '',
                '>' => '',
                '|' => '',
                '.' => '',
                "\n" => '',
                "\t" => '',
                '!' => '',
                '+' => $changeExceptionToNames ? 'plus' : '',
                '*' => $changeExceptionToNames ? 'star' : '',
                '&' => $changeExceptionToNames ? 'and' : '',
            ]
        );
    }

    private function unaccent($string)
    {
        $unwanted_array = [
            'Š' =>'S', 'š' =>'s', 'Ž' =>'Z', 'ž' =>'z', 'À' =>'A', 'Á' =>'A', 'Â' =>'A', 'Ã' =>'A', 'Ä' =>'A', 'Å' =>'A', 'Æ' =>'A', 'Ç' =>'C', 'È' =>'E', 'É' =>'E',
            'Ê' =>'E', 'Ë' =>'E', 'Ì' =>'I', 'Í' =>'I', 'Î' =>'I', 'Ï' =>'I', 'Ñ' =>'N', 'Ò' =>'O', 'Ó' =>'O', 'Ô' =>'O', 'Õ' =>'O', 'Ö' =>'O', 'Ø' =>'O', 'Ù' =>'U',
            'Ú' =>'U', 'Û' =>'U', 'Ü' =>'U', 'Ý' =>'Y', 'Þ' =>'B', 'ß' =>'Ss', 'à' =>'a', 'á' =>'a', 'â' =>'a', 'ã' =>'a', 'ä' =>'a', 'å' =>'a', 'æ' =>'a', 'ç' =>'c',
            'è' =>'e', 'é' =>'e', 'ê' =>'e', 'ë' =>'e', 'ì' =>'i', 'í' =>'i', 'î' =>'i', 'ï' =>'i', 'ð' =>'o', 'ñ' =>'n', 'ò' =>'o', 'ó' =>'o', 'ô' =>'o', 'õ' =>'o',
            'ö' =>'o', 'ø' =>'o', 'ù' =>'u', 'ú' =>'u', 'û' =>'u', 'ý' =>'y', 'þ' =>'b', 'ÿ' =>'y'
        ];

        return strtr($string, $unwanted_array);
    }
}
