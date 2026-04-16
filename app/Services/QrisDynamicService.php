<?php

namespace App\Services;

use App\Models\Sale;
use Crc16\Crc16;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrisDynamicService
{
    public function generateDynamicQris(Sale $sale)
    {
        $statisQris = "00020101021126610014COM.GO-JEK.WWW01189360091434313192070210G4313192070303UKE51440014ID.CO.QRIS.WWW0215ID10243345075540303UKE5204573253033605802ID5923prahata service & store6013JAKARTA PUSAT61051023062070703A016304B5B5";

        $qris = substr($statisQris, 0, strlen($statisQris)-4);

        $step1 = str_replace('010211', '010212', $qris);

        $step2 = explode('5802ID', $step1);

        $total_amount = $sale->total_amount;

        $uang = "54" . str_pad(strlen($total_amount), 2, '0', STR_PAD_LEFT) . $total_amount;

        $uangNew = $uang . "5802ID";

        $dynamicQris = trim($step2[0]) . $uangNew . trim($step2[1]);

        $crc = $this->crc16($dynamicQris);

        return [
            'qr_string' => $dynamicQris . $crc,
            'qr_static' => $statisQris,
        ];
    }

    public function crc16($str) {
        $charCodeAt = function($str, $index) {
            return ord($str[$index]);
        };

        $crc = 0xFFFF;
        $strLen = strlen($str);

        for ($i = 0; $i < $strLen; $i++) {
            $crc ^= $charCodeAt($str, $i) << 8;

            for ($j = 0; $j < 8; $j++) {
                if ($crc & 0x8000) {
                    $crc = ($crc << 1) ^ 0x1021;
                } else {
                    $crc <<= 1;
                }
            }
        }

        $hex = $crc &= 0xFFFF;

        $hexString = strtoupper(base_convert($hex, 10, 16));

        if (strlen($hexString) == 3) {
            $hexString = '0' . $hexString;
        }

        return $hexString;
    }
}
