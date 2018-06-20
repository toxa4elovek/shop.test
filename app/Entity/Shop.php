<?php

namespace App\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

/**
 * Class Shop
 * @property integer $id
 * @property string $title
 * @property integer $regionId
 * @property string $city
 * @property string $address
 * @property integer $userId
 * @package App\Entity
 */
class Shop extends Model
{

    protected $fillable = ['title', 'regionId', 'city', 'address', 'userId'];

    public $timestamps = false;

    public function setRegionId(int $value): void
    {
        $this->regionId = $value;
    }

    public function setTitle(string $value): void
    {
        $this->title = $value;
    }

    public function setCity(string $value): void
    {
        $this->city = $value;
    }

    public function setAddress(string $value): void
    {
        $this->address = $value;
    }

    public function setUserId(string $value): void
    {
        $this->userId = $value;
    }

    public function getRegionId(): int
    {
        return $this->regionId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * Метод производит валидацию данных
     * @param $data
     * @return bool
     */
    public function validation($data): bool
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
