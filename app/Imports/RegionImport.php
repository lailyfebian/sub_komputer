<?php

namespace App\Imports;

use App\Models\Region;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class RegionImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $name = trim($row['nama_wilayah'] ?? '');
        $type = trim($row['tipe'] ?? '');
        $address = trim($row['alamat'] ?? '');
        $parentName = trim($row['parent_name'] ?? '');
        $parentType = trim($row['parent_type'] ?? '');

        if (!$name || !$type) {
            return null; // lewati baris kosong atau tidak lengkap
        }

        // Cari atau buat parent (jika ada)
        $parent = null;
        if ($parentName && $parentType) {
            $parent = Region::firstOrCreate(
                [
                    'name' => $parentName,
                    'type' => $parentType,
                ],
                [
                    'slug' => Str::slug($parentName),
                    'address' => null,
                    'parent_id' => $this->findParentIdFor($parentType, $parentName),
                ]
            );
        }

        // Cek apakah sudah ada data serupa
        $exists = Region::where('name', $name)
            ->where('type', $type)
            ->when($parent, fn($q) => $q->where('parent_id', $parent->id))
            ->exists();

        if ($exists) {
            return null;
        }

        $region = new Region([
            'name' => $name,
            'type' => $type,
            'address' => $address,
            'parent_id' => $parent?->id,
        ]);

        // Generate slug hierarkis
        $region->slug = $this->generateSlug($region);

        return $region;
    }

    /**
     * Cari parent_id yang cocok untuk parent type yang lebih tinggi
     */
    private function findParentIdFor($type, $name)
    {
        // Tentukan tipe parent berdasarkan hierarki
        $expectedParentType = match ($type) {
            'kecamatan' => 'kota',
            'kelurahan', 'desa' => 'kecamatan',
            default => null,
        };

        if (!$expectedParentType) {
            return null;
        }

        $parent = Region::where('type', $expectedParentType)
            ->where('name', $name)
            ->first();

        return $parent?->id;
    }

    /**
     * Generate slug hierarkis (parent → child)
     */
    private function generateSlug(Region $region)
    {
        $names = [$region->name];
        $parent = $region->parent;

        while ($parent) {
            array_unshift($names, $parent->name);
            $parent = $parent->parent;
        }

        return Str::slug(implode('-', $names));
    }
}
