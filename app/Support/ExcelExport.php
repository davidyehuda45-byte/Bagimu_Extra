<?php

namespace App\Support;

use Symfony\Component\HttpFoundation\StreamedResponse;

class ExcelExport
{
    /**
     * Excel SpreadsheetML 2003 (.xls) tanpa dependensi — bisa dibuka
     * MS Excel/LibreOffice. Netralisir formula-injection seperti CSV.
     */
    public static function xls(string $filename, array $headers, iterable $rows): StreamedResponse
    {
        $cell = function ($v): string {
            $s = (string) ($v ?? '');
            if (preg_match('/^[=+\-@\t\r]/', $s)) {
                $s = "'".$s;
            }

            return '<Cell><Data ss:Type="String">'.htmlspecialchars($s, ENT_QUOTES | ENT_XML1, 'UTF-8').'</Data></Cell>';
        };

        return response()->streamDownload(function () use ($headers, $rows, $cell) {
            echo '<?xml version="1.0" encoding="UTF-8"?>';
            echo '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">';
            echo '<Worksheet ss:Name="Pendaftar"><Table>';
            echo '<Row>';
            foreach ($headers as $h) {
                echo $cell($h);
            }
            echo '</Row>';
            foreach ($rows as $row) {
                $row = is_array($row) ? $row : $row->toArray();
                echo '<Row>';
                foreach ($row as $v) {
                    echo $cell($v);
                }
                echo '</Row>';
            }
            echo '</Table></Worksheet></Workbook>';
        }, $filename, ['Content-Type' => 'application/vnd.ms-excel']);
    }
}
