<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UtilController extends Controller
{
    public static function getSlotJam($durasi, $nomor_slot)
    {
        if ($durasi == '01:00:00') {
            $durasi = 3600;
        } elseif ($durasi == '01:30:00') {
            $durasi = 5400;
        } elseif ($durasi == '02:00:00') {
            $durasi = 7200;
        }
        $mulai = strtotime("08:00:00");
        $batasSelesai = strtotime("16:00:00");
        $slotJadwal = array();
        $jumlahSlot = 1;
        while ($mulai <= $batasSelesai) {
            $selesai = $mulai + $durasi;
            if ($selesai <= $batasSelesai) {
                if ($mulai == strtotime("12:00:00")) {
                    $mulai += 3600;
                    $selesai += 3600;
                    $jadwal = array("id" => $jumlahSlot, "slot" => date('H:i', $mulai) . "-" . date('H:i', $selesai));
                } else if ($mulai == strtotime("12:30:00")) {
                    $mulai += 1800;
                    $selesai += 1800;
                    $jadwal = array("id" => $jumlahSlot, "slot" => date('H:i', $mulai) . "-" . date('H:i', $selesai));
                } else {
                    $jadwal = array("id" => $jumlahSlot, "slot" => date('H:i', $mulai) . "-" . date('H:i', $selesai));
                }
                $slotJadwal[] = $jadwal;
            }
            $mulai = $selesai;
            $jumlahSlot += 1;
        }
        $slotjam = "";
        foreach ($slotJadwal as $sj) {
            if ($sj['id'] == $nomor_slot) {
                $slotjam = $sj['slot'];
            }
        }
        return $slotjam;
    }

    public static function getAllSlots($durasi)
    {
        if ($durasi == '01:00:00') {
            $durasi = 3600;
        } elseif ($durasi == '01:30:00') {
            $durasi = 5400;
        } elseif ($durasi == '02:00:00') {
            $durasi = 7200;
        }
        $mulai = strtotime("08:00:00");
        $batasSelesai = strtotime("16:00:00");
        $slotJadwal = array();
        $jumlahSlot = 1;
        while ($mulai <= $batasSelesai) {
            $selesai = $mulai + $durasi;

            if ($selesai <= $batasSelesai) {
                if ($mulai == strtotime("12:00:00")) {
                    $mulai += 3600;
                    $selesai+=3600;
                    $jadwal = array("id" => $jumlahSlot, "slot" => date('H:i', $mulai) . "-" . date('H:i', $selesai));
                } else if ($mulai == strtotime("12:30:00")) {
                    $mulai += 1800;
                    $selesai +=1800;
                    $jadwal = array("id" => $jumlahSlot, "slot" => date('H:i', $mulai) . "-" . date('H:i', $selesai));
                } else {
                    $jadwal = array("id" => $jumlahSlot, "slot" => date('H:i', $mulai) . "-" . date('H:i', $selesai));
                }
                $slotJadwal[] = $jadwal;
            }
            $mulai = $selesai;
            $jumlahSlot += 1;
        }
        return $slotJadwal;
    }
}
