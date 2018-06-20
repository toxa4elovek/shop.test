<?php
/**
 *
 */

namespace App\Services;


use App\Entity\Shop;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * Class ImportCsvService
 * @package App\Services
 */
class ImportCsvService
{
    const UTF_FILE_ENCODING = 'utf-8';
    const CP_FILE_EnCODING = 'cp-1251';

    /**
     * Метод импортирует данные из csv файла в БД
     *
     * @param string $filePath
     * @return array
     * @throws FileNotFoundException
     */
    public function import(string $filePath): array
    {
        if(!file_exists($filePath)) {
            throw new FileNotFoundException('File does not exist');
        }

        return $this->prepareData($this->readFile($filePath));
    }

    /**
     * Метод сохраняет данные в базу
     *
     * @param array $data
     * @return int
     */
    public function save(array $data): int
    {
        $recordsCount = 0;

        foreach ($data as $row) {

            /**@var Shop $shop*/
            $shop = new Shop();
            $shop->setRegionId($row['REGION_ID']);
            $shop->setTitle($row['TITLE']);
            $shop->setCity($row['CITY']);
            $shop->setAddress($row['ADDR']);
            $shop->setUserId($row['USER_ID']);

            if (!$shop->validation($row)) {
                continue;
            }

            $recordsCount += $shop->save() ? 1 : 0;
        }

        return $recordsCount;
    }

    /**
     * Метод подготавливает данные для записи в базу
     * @param array $data
     * @return array
     */
    private function prepareData(array $data): array
    {
        $head = array_shift($data);

        return array_map(function($row) use ($head){
            return array_combine($head, $row);
        }, $data);
    }

    /**
     * Метод меняет кодировку данных в файле если они не в utf-8
     *
     * @param $filePath
     * @return array
     */
    private function readFile($filePath): array
    {
        $resource = fopen($filePath, 'r');
        $result = [];

        if ($resource) {
            while (($buffer = fgets($resource, 4096)) !== false) {
                $encoding = mb_detect_encoding($buffer, null, true);

                if ($encoding === false) {
                    $buffer = mb_convert_encoding($buffer, self::UTF_FILE_ENCODING, self::CP_FILE_EnCODING);
                }

                $result[] = str_getcsv($buffer);

            }
            if (!feof($resource)) {
                throw new FileException('Error reading from file: "'. $filePath .'"');
            }

            fclose($resource);
        }

        return $result;
    }
}