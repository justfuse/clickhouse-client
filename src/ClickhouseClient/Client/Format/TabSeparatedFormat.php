<?php
/**
 * Created by PhpStorm.
 * User: bogdans
 * Date: 9/12/17
 * Time: 11:54 PM
 */

namespace ClickhouseClient\Client\Format;


class TabSeparatedFormat implements  FormatInterface
{

    /**
     * Returns format used for getting data from database
     *
     * @return string
     */
    public function queryFormat(): string
    {
        return 'TabSeparatedWithNames';
    }

    /**
     * Returns format used for inserting data into database
     *
     * @return string
     */
    public function insertFormat(): string
    {
        return 'TabSeparated';
    }

    /**
     * Encode single row of data
     * Generally is used for proper data inserting
     *
     * @param array $row
     * @return string
     */
    public function encode(array $row): string
    {
        $stream = fopen('php://memory', 'r+');
        fputcsv($stream, $row, "\t");
        rewind($stream);
        $line = fgets($stream);
        fclose($stream);
        return $line;
    }

    /**
     * Decode single string of data
     * Generally is used when decoding response from database
     *
     * @param string $row
     * @return array
     */
    public function decode(string $row): array
    {
        $stream = fopen('php://memory', 'r+');
        fputs($stream, $row);
        rewind($stream);
        $result = [];
        $names = fgetcsv($stream, 0, "\t");
        if (empty($names) === true) {
            throw new \RuntimeException('failed decoding names from response');
        }
        while(($row = fgetcsv($stream, 0, "\t")) !== false) {
            $result[] = array_combine($names, $row);
        }
        fclose($stream);
        return $result;
    }
}
