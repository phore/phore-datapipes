<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 26.04.19
 * Time: 12:55
 */

namespace Phore\DataPipes\InputFormat;


use Phore\FileSystem\FileStream;
use Phore\FileSystem\PhoreFile;

class CsvInput
{


    /**
     * @var FileStream
     */
    private $input;

    private $header = null;

    private $delimiter;

    private $commentChars = [];

    /**
     * CsvInput constructor.
     *
     * <example>
     *
     * $csv = new CsvInput("/path/to/file.csv")
     * $csv = new CsvInput(new PhoreFile(..))
     * $csv = new CsvInput(new FileStream())
     *
     * </example>
     *
     *
     * @param $input string|PhoreFile|FileStream    The input file
     * @param string $delimiter
     * @param string[] $ignoreComments  Ignore Lines starting with one of these chars.
     * @throws \Phore\FileSystem\Exception\FileAccessException
     */
    public function __construct($input, string $delimiter=",")
    {
        if (is_string($input))
            $input = phore_file($input)->fopen("r");
        if ($input instanceof PhoreFile)
            $input = $input->fopen("r");
        if ( ! $input instanceof FileStream)
            throw new \InvalidArgumentException("Invalid parameter 1 for __construct(): Must be FileStream, PhoreFile or string filename");
        $this->input = $input;
        $this->delimiter = $delimiter;
    }


    /**
     * Ingore Lines starting with each of these chars
     *
     * @param array $commentChars
     * @return CsvInput
     */
    public function setIgnoreLinesStartingWith($commentChars = ["#"]) : self
    {
        $this->commentChars = $commentChars;
        return $this;
    }

    private function isCommentLine(array $input)
    {
        if (count ($input) === 0)
            return true;
        if (count($input) === 1 && trim ($input[0]) === "")
            return true;
        $firstChar = substr($input[0], 0, 1);
        if (in_array($firstChar, $this->commentChars))
            return true;
        return false;
    }


    public function readPlainText(int $lines) : string
    {
        $buf = "";
        for ($i=0; $i<$lines; $i++) {
            if ($this->input->feof())
                throw new \InvalidArgumentException("Premature end of input file while reading plain text ($lines lines)");
            $buf .= $this->input->fgets();
        }
        return $buf;
    }


    /**
     * Read the first line and treat it as header (assoc)
     *
     * Triggers exception if file is empty
     *
     * @param int $requireMinCols
     * @throws \Phore\FileSystem\Exception\FileAccessException
     */
    public function readHeader(int $requireMinCols=1) : self
    {
        while (true) {
            if ($this->input->feof())
                throw new \InvalidArgumentException("No header found in csv stream.");
            $cols = $this->input->freadcsv(0, $this->delimiter);
            if ($cols === null) {
                $cols = [];
            }
            if ($this->isCommentLine($cols))
                continue;
            break;
        }
        if (count($cols) < $requireMinCols)
            throw new \InvalidArgumentException("Header requires $requireMinCols columns: Found:" . print_r($cols, true) );
        $this->header = $cols;
        return $this;
    }

    /**
     * <example>
     * $csv->setColumnNames(["name", "surname", "birthdate"]);
     * </example>
     *
     * @param array $columnNames
     */
    public function setColumnNames(array $columnNames) {
        $this->header = $columnNames;
    }


    /**
     *
     * Returns a generator
     *
     * <example>
     * foreach($csv->getData() as $row) {
     *    echo $row["name"] . $row["surname"]
     * }
     * </example>
     *
     *
     * @param bool $strict
     * @return \Generator
     * @throws \Phore\FileSystem\Exception\FileAccessException
     */
    public function getData(bool $strict=true) : \Generator
    {
        if ( ! $this->input->isOpen())
            throw new \InvalidArgumentException("The file is already closed. Use getData() only once!");
        while ( ! $this->input->feof()) {
            $data = $this->input->freadcsv(0, $this->delimiter);
            if ($data === null)
                break;
            if ($this->isCommentLine($data))
                continue;

            if ($this->header === null) {
                yield $data;
                continue;
            }
            if ($strict) {
                if (($inputCount = count($data)) !== ($headerCount = count($this->header))) {
                    throw new \InvalidArgumentException("StrictMode: Input column count '$inputCount' mismatches header count '$headerCount': " . print_r($data, true));
                }
            }
            $ret = [];
            foreach ($this->header as $index => $name) {
                $ret[$name] = $data[$index];
            }
            yield $ret;
        }
        $this->input->fclose();
    }

}
