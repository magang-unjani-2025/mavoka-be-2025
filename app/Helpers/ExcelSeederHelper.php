<?php

namespace App\Helpers;

use Maatwebsite\Excel\Facades\Excel;

class ExcelSeederHelper
{
    public static function loadFirstSheet(string $path): array
    {
        $sheets = Excel::toArray(new GenericImport, $path);
        return $sheets[0] ?? [];
    }

    public static function findHeaderIndex(array $rows): ?int
    {
        foreach ($rows as $i => $r) {
            if (collect($r)->filter(fn($v) => trim((string)$v) !== '')->isNotEmpty()) return $i;
        }
        return null;
    }

    public static function normalizeHeader(string $v): string
    {
        $v = trim($v);
        $v = preg_replace('/\xEF\xBB\xBF/', '', $v);
        $v = strtolower($v);
        $v = preg_replace('/[^a-z0-9]+/i', '_', $v);
        return trim($v, '_');
    }

    public static function mapHeaders(array $rawHeaders, array $columnMap): array
    {
        $resolved = [];
        foreach ($rawHeaders as $idx => $h) {
            $hn = self::normalizeHeader((string)$h);
            foreach ($columnMap as $field => $aliases) {
                $aliasesNorm = array_map(fn($a) => self::normalizeHeader($a), $aliases);
                if (in_array($hn, $aliasesNorm, true)) { $resolved[$idx] = $field; break; }
            }
            if (!isset($resolved[$idx]) && isset($columnMap[$hn])) $resolved[$idx] = $hn;
        }
        return $resolved;
    }

    public static function parseDateFlexible($value): ?string
    {
        if ($value === null || $value === '') return null;
        if (is_numeric($value)) {
            try {
                $base = \Carbon\Carbon::createFromFormat('Y-m-d', '1899-12-30');
                return $base->copy()->addDays((int)$value)->format('Y-m-d');
            } catch (\Throwable $e) {}
        }
        $formats = ['Y-m-d','d/m/Y','d-m-Y','d.m.Y','Y/m/d'];
        foreach ($formats as $f) {
            try {
                $dt = \Carbon\Carbon::createFromFormat($f, (string)$value, 'UTC');
                if ($dt !== false) return $dt->format('Y-m-d');
            } catch (\Throwable $e) {}
        }
        try { return \Carbon\Carbon::parse($value)->format('Y-m-d'); } catch (\Throwable $e) { return null; }
    }
}
