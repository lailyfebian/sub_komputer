<?php

namespace App\Imports;

use App\Models\Region;
use App\Models\specificAddresses;
use App\Models\schoolAddresses;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Row;

class SpecificAddressImport implements OnEachRow, WithHeadingRow
{
    public function onRow(Row $row)
    {
        $data = $row->toArray();

        $type = trim($data['tipe_wilayah'] ?? '');
        $kotaName = trim($data['kota'] ?? '');
        $kecamatanName = trim($data['kecamatan'] ?? '');
        $category = trim($data['kategori'] ?? '');
        $schoolName = trim($data['nama_sekolah'] ?? '');
        $address = trim($data['alamat'] ?? '');

        if (
            !$type ||
            !$kotaName ||
            !$category ||
            !$address
        ) {
            return;
        }

        if (!in_array($type, ['kota', 'kecamatan'])) {
            return;
        }

        /*
         * Cari region
         */
        if ($type === 'kota') {

            $region = Region::where('name', $kotaName)
                ->where('type', 'kota')
                ->first();

        } else {

            if (!$kecamatanName) {
                return;
            }

            $kota = Region::where('name', $kotaName)
                ->where('type', 'kota')
                ->first();

            if (!$kota) {
                return;
            }

            $region = Region::where('name', $kecamatanName)
                ->where('type', 'kecamatan')
                ->where('parent_id', $kota->id)
                ->first();
        }

        if (!$region) {
            return;
        }

        /*
         * Kategori yang diperbolehkan
         */
        $allowedCategories = [
            'sd_negeri',
            'smp_negeri',
            'sma_negeri',
            'sd_swasta',
            'smp_swasta',
            'sma_swasta',
            'mts',
            'ma',
        ];

        if (!in_array($category, $allowedCategories)) {
            return;
        }

        /*
         * Jumlah kategori maksimal
         */
        $maxCategories = $type === 'kota' ? 8 : 4;

        $specificAddress = specificAddresses::where('region_id', $region->id)
            ->where('category', $category)
            ->first();

        /*
         * Jika kategori belum ada,
         * cek apakah region sudah mencapai
         * batas jumlah kategori.
         */
        if (!$specificAddress) {

            $categoryCount = specificAddresses::where(
                'region_id',
                $region->id
            )->count();

            if ($categoryCount >= $maxCategories) {
                return;
            }

            $specificAddress = specificAddresses::create([
                'region_id' => $region->id,
                'category' => $category,
            ]);
        }

        /*
         * Maksimal 5 alamat sekolah
         * per kategori
         */
        $schoolCount = schoolAddresses::where(
            'specific_address_id',
            $specificAddress->id
        )->count();

        if ($schoolCount >= 5) {
            return;
        }

        /*
         * Simpan alamat sekolah
         */
        SchoolAddresses::create([
            'specific_address_id' => $specificAddress->id,
            'school_name' => $schoolName,
            'address' => $address,
        ]);
    }
}