<?php

namespace App\Utils;

use Nette\Application\Response;
use Nette\Http\IRequest;
use Nette\Http\IResponse;

/**
 * @method self deleteHeader(string $name)
 */
final class CsvResponse implements Response
{
    const S302_FOUND = 302;
    private string $fileName;
    private iterable $rows; /** @phpstan-ignore-line */
    private string $delimiter;

    /** @phpstan-ignore-next-line */
    public function __construct(string $fileName, iterable $rows, string $delimiter = ';')
    {
        $this->fileName = $fileName;
        $this->rows = $rows;
        $this->delimiter = $delimiter;
    }

    public function send(IRequest $request, IResponse $response): void
    {
        $response->setContentType('text/csv', 'utf-8');

        $tmp = str_replace('"', "'", $this->fileName);
        $response->setHeader(
            'Content-Disposition',
            "attachment; filename=\"$tmp\"; filename*=utf-8''" . rawurlencode($this->fileName)
        );

        $bom = true;
        $fd = fopen('php://output', 'wb');

        foreach ($this->rows as $row) {
            if ($bom) {
                fputs($fd, "\xEF\xBB\xBF");
                $bom = false;
            }

            $row = $row instanceof \Traversable ? iterator_to_array($row) : (array) $row;
            fputcsv($fd, $row, $this->delimiter);
        }

        fclose($fd);
    }
}
