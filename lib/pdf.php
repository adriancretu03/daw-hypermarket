<?php

function pdf_from_lines(array $lines, int $linesPerPage = 40): string
{
    $pages = array_chunk($lines === [] ? [''] : $lines, $linesPerPage);

    $objects = [];
    $objects[1] = '<< /Type /Catalog /Pages 2 0 R >>';

    $fontObjNum = 3;
    $objects[$fontObjNum] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>';

    $pageObjNums = [];
    $nextObjNum = 4;

    foreach ($pages as $pageLines) {
        $contentObjNum = $nextObjNum++;
        $pageObjNum = $nextObjNum++;
        $pageObjNums[] = $pageObjNum;

        $stream = pdf_build_content_stream($pageLines);
        $objects[$contentObjNum] = "<< /Length " . strlen($stream) . " >>\nstream\n{$stream}\nendstream";
        $objects[$pageObjNum] = '<< /Type /Page /Parent 2 0 R /Resources << /Font << /F1 '
            . $fontObjNum . " 0 R >> >> /MediaBox [0 0 595 842] /Contents {$contentObjNum} 0 R >>";
    }

    $kids = implode(' ', array_map(static fn (int $n): string => "{$n} 0 R", $pageObjNums));
    $objects[2] = "<< /Type /Pages /Kids [{$kids}] /Count " . count($pageObjNums) . ' >>';

    ksort($objects);

    $pdf = "%PDF-1.4\n";
    $offsets = [];

    foreach ($objects as $num => $body) {
        $offsets[$num] = strlen($pdf);
        $pdf .= "{$num} 0 obj\n{$body}\nendobj\n";
    }

    $xrefOffset = strlen($pdf);
    $count = max(array_keys($objects)) + 1;
    $pdf .= "xref\n0 {$count}\n0000000000 65535 f \n";

    for ($n = 1; $n < $count; $n++) {
        $pdf .= sprintf("%010d 00000 n \n", $offsets[$n] ?? 0);
    }

    $pdf .= "trailer\n<< /Size {$count} /Root 1 0 R >>\nstartxref\n{$xrefOffset}\n%%EOF";

    return $pdf;
}

function pdf_build_content_stream(array $lines): string
{
    $y = 800;
    $content = "BT\n/F1 11 Tf\n";

    foreach ($lines as $line) {
        $escaped = pdf_escape_text((string) $line);
        $content .= "1 0 0 1 40 {$y} Tm\n({$escaped}) Tj\n";
        $y -= 14;
    }

    $content .= 'ET';

    return $content;
}

function pdf_escape_text(string $text): string
{
    return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
}
