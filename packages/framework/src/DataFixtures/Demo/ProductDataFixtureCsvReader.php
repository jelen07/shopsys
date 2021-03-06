<?php

namespace Shopsys\FrameworkBundle\DataFixtures\Demo;

use Shopsys\FrameworkBundle\Component\Csv\CsvReader;
use Shopsys\FrameworkBundle\Component\String\EncodingConverter;
use Shopsys\FrameworkBundle\Component\String\TransformString;

class ProductDataFixtureCsvReader
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Csv\CsvReader
     */
    protected $csvReader;

    /**
     * @param string $path
     * @param \Shopsys\FrameworkBundle\Component\Csv\CsvReader $csvReader
     */
    public function __construct(
        $path,
        CsvReader $csvReader
    ) {
        $this->path = $path;
        $this->csvReader = $csvReader;
    }

    /**
     * @return array
     */
    public function getProductDataFixtureCsvRows()
    {
        $rawRowsWithHeader = $this->csvReader->getRowsFromCsv($this->path);
        $rawRows = array_slice($rawRowsWithHeader, 1);
        $rows = array_map(function ($rawRow) {
            return $this->prepareRawRow($rawRow);
        }, $rawRows);

        return $rows;
    }

    /**
     * @param array $rawRow
     * @return array mixed
     */
    protected function prepareRawRow($rawRow)
    {
        $row = array_map([TransformString::class, 'emptyToNull'], $rawRow);

        return EncodingConverter::cp1250ToUtf8($row);
    }
}
