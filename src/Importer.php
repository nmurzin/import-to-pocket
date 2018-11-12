<?php

namespace ImportToPocket;

class Importer
{
    private $readerResult;
    private $accessToken;

    public function __construct(array $readerResult, string $accessToken)
    {
        $this->readerResult = $readerResult;
        $this->accessToken = $accessToken;
    }
}
