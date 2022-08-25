<?php

namespace App\Actions;

use Box\Spout\Reader\SheetInterface;

/**
 *
 * Inspired by: https://github.com/box/spout/issues/368
 *
 * Simple helper class for Spout - to return rows indexed by the header in the sheet
 *
 * Author: Jaspal Singh - https://github.com/jaspal747
 * Feel free to make any edits as needed. Cheers!
 *
 */
class SpoutHelper
{
    /**
     * Local array to hold the Raw Headers for performance.
     *
     * @var array
     */
    private $rawHeadersArray = [];

    /**
     * Local array to hold the Formatted Headers for performance.
     *
     * @var array
     */
    private $formattedHeadersArray = [];

    /**
     * Row number where the header col is located in the file.
     *
     * @var int
     */
    private $headerRowNumber;

    /**
     * Initialize on a per sheet basis
     * Allow users to mention which row number contains the headers
     *
     * @param  SheetInterface  $sheet
     * @param  int            $headerRowNumber
     */
    public function __construct(SheetInterface $sheet, $headerRowNumber = 1)
    {
        $this->flushHeaders();
        $this->headerRowNumber = $headerRowNumber;

        // Since this also calls the getRawHeaders, we will have both the arrays set at once
        $this->getFormattedHeaders($sheet);
    }

    /**
     *
     * Set the rawHeadersArray by getting the raw headers from the headerRowNumber or the 1st row
     * Once done, set them to a local variable for being reused later
     *
     * @param  SheetInterface  $sheet
     * @return array
     */
    public function getRawHeaders(SheetInterface $sheet): array
    {
        if (! empty($this->rawHeadersArray)) {
            return $this->rawHeadersArray;
        }

        // first get column headers
        foreach ($sheet->getRowIterator() as $key => $row) {
            if ($key == $this->headerRowNumber) {
                /**
                 * iterate once to get the column headers
                 */
                $this->rawHeadersArray = $row->toArray();
                break;
            }
        }

        return $this->rawHeadersArray;
    }

    /**
     *
     * Set the formattedHeadersArray by getting the raw headers and the parsing them
     * Once done, set them to a local variable for being reused later
     *
     * @param  SheetInterface  $sheet
     * @return array
     */
    public function getFormattedHeaders(SheetInterface $sheet): array
    {
        if (! empty($this->formattedHeadersArray)) {
            return $this->formattedHeadersArray;
        }

        $this->formattedHeadersArray = $this->getRawHeaders($sheet);

        /**
         * Now format them
         */
        foreach ($this->formattedHeadersArray as $key => $value) {
            // Somehow instanceOf does not work well with DateTime, hence using is_a -- ?
            if (is_a($value, 'DateTime')) {
                // Since the dates in headers are avilable as DateTime Objects
                $this->formattedHeadersArray[$key] = $value->format('Y-m-d');
            } else {
                $this->formattedHeadersArray[$key] = strtolower(str_replace(' ', '_', trim($value)));
            }
        }

        return $this->formattedHeadersArray;
    }

    /**
     * Return row with Raw Headers.
     *
     * @param  array  $rowArray
     * @return array
     */
    public function rowWithRawHeaders(array $rowArray): array
    {
        return $this->returnRowWithHeaderAsKeys($this->rawHeadersArray, $rowArray);
    }

    /**
     * Return row with Formatted Headers.
     *
     * @param  array  $rowArray
     * @return array
     */
    public function rowWithFormattedHeaders(array $rowArray): array
    {
        return $this->returnRowWithHeaderAsKeys($this->formattedHeadersArray, $rowArray);
    }

    /**
     * Set the headers to keys and row as values.
     *
     * @param  array  $headers
     * @param  array  $rowArray
     * @return array
     */
    private function returnRowWithHeaderAsKeys(array $headers, array $rowArray): array
    {
        $headerColCount = count($headers);
        $rowColCount = count($rowArray);
        $colCountDiff = $headerColCount - $rowColCount;


        if ($colCountDiff > 0) {
            // Pad the rowArray with empty values
            $rowArray = array_pad($rowArray, $headerColCount, '');
        }

        return array_combine($headers, $rowArray);
    }

    /**
     * Flush local caches before each sheet.
     *
     * @return void
     */
    public function flushHeaders(): void
    {
        $this->formattedHeadersArray = [];
        $this->rawHeadersArray = [];
    }
}
