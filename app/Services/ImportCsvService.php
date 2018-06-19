<?php
/**
 *
 */

namespace App\Services;


use App\Entity\Shop;
use Illuminate\Support\Facades\Validator;
use League\Csv\Reader;

class ImportCsvService
{
    /**
     * Метод импортирует данные из csv файла в БД
     * @param string $filePath
     * @return int
     */
    public function import(string $filePath): int
    {
        if(!file_exists($filePath)) {
            throw new \DomainException('File does not exist');
        }
        $this->prepareFileEncoding($filePath);
        $data = $this->prepareData($this->read($filePath));
        $count = 0;

        foreach ($data as $row) {
            dd($row);
            if (!$this->validation($row)) {
                continue;
            }

            $result = Shop::create([
                'regionId' => $row['REGION_ID'],
                'title' => $row['TITLE'],
                'city' => $row['CITY'],
                'address' => $row['ADDR'],
                'userId' => $row['USER_ID']
            ]);

            $count += $result ? 1 : 0;
        }

        return $count;
    }

    /**
     * Метод считывает данные из файла и преобразовывает в массив
     * @param string $filePath
     * @return array
     */
    private function read(string $filePath): array
    {
        return Reader::createFromPath($filePath)->setInputEncoding('CP-1251')->fetchAll();
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
     */
    private function prepareFileEncoding($filePath): void
    {
        $content  = file_get_contents($filePath);
        $encoding = mb_detect_encoding($content, null, true);

        if ($encoding === false) {
            file_put_contents($filePath, mb_convert_encoding($content, 'utf-8', 'cp-1251'));
        }
    }

    /**
     * Метод производит валидацию данных
     * @param $data
     * @return bool
     */
    private function validation($data): bool
    {
        return !Validator::make($data, [
            'REGION_ID' => 'required|integer',
            'TITLE'     => 'required|string|max:255',
            'CITY'      => 'required|string|max:255' ,
            'ADDR'      => 'required|string|max:255',
            'USER_ID'   => 'required|integer',
        ])->fails();
    }
}