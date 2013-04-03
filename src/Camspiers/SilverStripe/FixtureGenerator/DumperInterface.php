<?php

namespace Camspiers\SilverStripe\FixtureGenerator;

interface DumperInterface
{
    /**
     * @param array $data
     */
    public function dump(array $data);
}
