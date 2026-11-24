<?php

namespace App\Support;

/**
 * Selangor administrative districts used by campaign creation and map display.
 * Legacy venue/locality values are mapped so existing campaigns remain visible.
 */
final class SelangorDistricts
{
    private const DISTRICTS = [
        'Sabak Bernam',
        'Kuala Selangor',
        'Hulu Selangor',
        'Gombak',
        'Petaling',
        'Klang',
        'Kuala Langat',
        'Sepang',
        'Hulu Langat',
    ];

    private const LEGACY_ALIASES = [
        'sabak bernam' => 'Sabak Bernam',
        'sungai besar' => 'Sabak Bernam',
        'sekinchan' => 'Sabak Bernam',

        'kuala selangor' => 'Kuala Selangor',
        'bestari jaya' => 'Kuala Selangor',
        'jeram' => 'Kuala Selangor',
        'ijok' => 'Kuala Selangor',

        'hulu selangor' => 'Hulu Selangor',
        'ulu selangor' => 'Hulu Selangor',
        'kuala kubu bharu' => 'Hulu Selangor',
        'batang kali' => 'Hulu Selangor',
        'hulu yam' => 'Hulu Selangor',
        'kerling' => 'Hulu Selangor',

        'gombak' => 'Gombak',
        'selayang' => 'Gombak',
        'batu caves' => 'Gombak',
        'rawang' => 'Gombak',

        'petaling' => 'Petaling',
        'petaling jaya' => 'Petaling',
        'subang jaya' => 'Petaling',
        'puchong' => 'Petaling',
        'damansara' => 'Petaling',
        'kelana jaya' => 'Petaling',
        'seri kembangan' => 'Petaling',
        'seri petaling' => 'Petaling',
        'shah alam' => 'Petaling',

        'klang' => 'Klang',
        'port klang' => 'Klang',
        'bukit tinggi' => 'Klang',

        'kuala langat' => 'Kuala Langat',
        'banting' => 'Kuala Langat',
        'jenjarom' => 'Kuala Langat',
        'telok panglima garang' => 'Kuala Langat',

        'sepang' => 'Sepang',
        'cyberjaya' => 'Sepang',
        'putra perdana' => 'Sepang',
        'salak tinggi' => 'Sepang',

        'hulu langat' => 'Hulu Langat',
        'ulu langat' => 'Hulu Langat',
        'kajang' => 'Hulu Langat',
        'cheras' => 'Hulu Langat',
        'semenyih' => 'Hulu Langat',
        'bandar baru bangi' => 'Hulu Langat',
        'balakong' => 'Hulu Langat',
        'ampang' => 'Hulu Langat',
    ];

    public static function all(): array
    {
        return self::DISTRICTS;
    }

    public static function resolve(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        $key = strtolower(trim($value));
        $key = preg_replace('/[^a-z0-9]+/', ' ', $key);
        $key = trim(preg_replace('/\s+/', ' ', $key));

        return self::LEGACY_ALIASES[$key] ?? null;
    }

    /**
     * Return current district names plus known legacy locality values that
     * should be included when filtering campaigns by an administrative district.
     */
    public static function databaseValuesFor(?string $district): array
    {
        $canonicalDistrict = self::resolve($district);

        if ($canonicalDistrict === null) {
            return [];
        }

        $values = [$canonicalDistrict];

        foreach (self::LEGACY_ALIASES as $legacyValue => $mappedDistrict) {
            if ($mappedDistrict === $canonicalDistrict) {
                $values[] = ucwords($legacyValue);
            }
        }

        return array_values(array_unique($values));
    }
}
