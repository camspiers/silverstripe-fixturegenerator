<?php

namespace Camspiers\SilverStripe\FixtureGenerator\Dumpers;

use Camspiers\SilverStripe\FixtureGenerator\DumperInterface;
use Symfony\Component\Yaml\Yaml as YamDumper;

class Yaml implements DumperInterface
{
    /**
     * @var string
     */
    private $filename;
    /**
     * @param $filename
     */
    public function __construct($filename)
    {
        $this->filename = $filename;
    }
    /**
     * @{@inheritdoc}
     */
    public function dump(array $data)
    {
        return file_put_contents($this->filename, YamDumper::dump($data));
    }
}
