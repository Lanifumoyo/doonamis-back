<?php

namespace Doonamis\User\Domain\Service;

use Iterator;
use League\Csv\Reader;

class GetUserDataFromCsvService
{
    public function execute(string $path): Iterator
    {
        $csv = Reader::createFromPath($path, 'r');
        $csv->setHeaderOffset(0);

        return $csv->getRecords();
    }
}