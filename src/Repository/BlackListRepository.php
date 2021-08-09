<?php

namespace App\Repository;

use App\Repository\Interfaces\BlackListRepositoryInterface;

class BlackListRepository implements BlackListRepositoryInterface
{
    /**
     * @var string[]
     * */
    private array $list = ['Иванов Иван Иванович'];

    /**
     * @return string[]
     */
    public function getAll(): array
    {
        return $this->list;
    }
}