<?php

namespace App\Services;

class SimplePdfGenerator
{
    private const PAGE_WIDTH = 595;
    private const PAGE_HEIGHT = 842;
    private const MARGIN_X = 40;
    private const START_Y = 800;
    private const LINE_HEIGHT = 16;
    private const MAX_CHARS_PER_LINE = 95;

    public function generate(string $title, array $lines): string
    {
        $allLines = array_merge([$title, str_repeat('=', 70), ''], $lines);
        $wrappedLines = $this->wrapLines($allLines);
        $pages = $this->paginateLines($wrappedLines);

        return $this->buildPdfDocument($pages);
    }

    private function wrapLines(array $lines): array
    {
        $wrapped = [];

        foreach ($lines as $line) {
            $line = (string) $line;
            if ($line === '') {
                $wrapped[] = '';
                continue;
            }

            $chunks = str_split($line, self::MAX_CHARS_PER_LINE);
            foreach ($chunks as $chunk) {
                $wrapped[] = $chunk;
            }
        }

        return $wrapped;
    }

    private function paginateLines(array $lines): array
    {
        $maxLinesPerPage = (int) floor((self::START_Y - 50) / self::LINE_HEIGHT);
        if ($maxLinesPerPage < 1) {
            $maxLinesPerPage = 1;
        }

        return array_chunk($lines, $maxLinesPerPage);
    }

    private function buildPdfDocument(array $pages): string
    {
        $objects = [];
        $pageObjectIds = [];

        $objects[] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>";
        $fontObjectId = 1;

        $nextObjectId = 2;

        foreach ($pages as $pageLines) {
            $content = $this->buildPageContent($pageLines);
            $contentObjectId = $nextObjectId++;
            $pageObjectId = $nextObjectId++;

            $objects[$contentObjectId - 1] = "<< /Length " . strlen($content) . " >>\nstream\n" . $content . "\nendstream";
            $objects[$pageObjectId - 1] = "<< /Type /Page /Parent {{PAGES_ID}} 0 R /MediaBox [0 0 " .
                self::PAGE_WIDTH . " " . self::PAGE_HEIGHT . "] /Resources << /Font << /F1 " . $fontObjectId .
                " 0 R >> >> /Contents " . $contentObjectId . " 0 R >>";

            $pageObjectIds[] = $pageObjectId;
        }

        $pagesObjectId = $nextObjectId++;
        $kids = implode(' ', array_map(fn($id) => $id . ' 0 R', $pageObjectIds));
        $objects[$pagesObjectId - 1] = "<< /Type /Pages /Kids [ " . $kids . " ] /Count " . count($pageObjectIds) . " >>";

        foreach ($pageObjectIds as $pageObjectId) {
            $objects[$pageObjectId - 1] = str_replace('{{PAGES_ID}}', (string) $pagesObjectId, $objects[$pageObjectId - 1]);
        }

        $catalogObjectId = $nextObjectId++;
        $objects[$catalogObjectId - 1] = "<< /Type /Catalog /Pages " . $pagesObjectId . " 0 R >>";

        return $this->assemblePdf($objects, $catalogObjectId);
    }

    private function buildPageContent(array $lines): string
    {
        $commands = [];
        $y = self::START_Y;

        foreach ($lines as $index => $line) {
            $fontSize = $index === 0 ? 14 : 11;
            $escaped = $this->escapePdfText($line);

            $commands[] = "BT /F1 {$fontSize} Tf " . self::MARGIN_X . " {$y} Td ({$escaped}) Tj ET";
            $y -= self::LINE_HEIGHT;
        }

        return implode("\n", $commands);
    }

    private function escapePdfText(string $text): string
    {
        return str_replace(
            ['\\', '(', ')'],
            ['\\\\', '\\(', '\\)'],
            $text
        );
    }

    private function assemblePdf(array $objects, int $catalogObjectId): string
    {
        $pdf = "%PDF-1.4\n";
        $offsets = [0];

        foreach ($objects as $index => $object) {
            $objectNumber = $index + 1;
            $offsets[$objectNumber] = strlen($pdf);
            $pdf .= $objectNumber . " 0 obj\n" . $object . "\nendobj\n";
        }

        $xrefOffset = strlen($pdf);
        $objectCount = count($objects) + 1;

        $pdf .= "xref\n";
        $pdf .= "0 " . $objectCount . "\n";
        $pdf .= "0000000000 65535 f \n";

        for ($i = 1; $i < $objectCount; $i++) {
            $offset = $offsets[$i] ?? 0;
            $pdf .= sprintf("%010d 00000 n \n", $offset);
        }

        $pdf .= "trailer\n";
        $pdf .= "<< /Size " . $objectCount . " /Root " . $catalogObjectId . " 0 R >>\n";
        $pdf .= "startxref\n";
        $pdf .= $xrefOffset . "\n";
        $pdf .= "%%EOF";

        return $pdf;
    }
}
