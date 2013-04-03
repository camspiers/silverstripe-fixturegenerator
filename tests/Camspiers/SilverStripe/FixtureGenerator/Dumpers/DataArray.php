<?php


namespace Camspiers\SilverStripe\FixtureGenerator\Dumpers;

use Camspiers\SilverStripe\FixtureGenerator\DumperInterface;

class DataArray implements DumperInterface
{
    /**
     * @param array $data
     * @return array
     */
    public function dump(array $data)
    {
        return $data;
    }

}