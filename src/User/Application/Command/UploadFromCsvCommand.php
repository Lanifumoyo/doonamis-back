<?php

namespace Doonamis\User\Application\Command;

use Illuminate\Http\UploadedFile;

class UploadFromCsvCommand
{
    public function __construct(
        public readonly UploadedFile $file
    ) {}
}