<?php

namespace Camspiers\SilverStripe\FixtureGenerator\Dumpers;

class GeneratorTest extends \PHPUnit_Framework_TestCase
{
    protected $yaml;
    protected $filename;
    public function setUp()
    {
        $this->filename = __DIR__ . '/test.yml';
        $this->yaml = new Yaml($this->filename);
    }
    public function tearDown()
    {
        unlink($this->filename);
    }
    public function testDump()
    {
        $this->assertFalse(file_exists($this->filename));
        $this->yaml->dump(array());
        $this->assertTrue(file_exists($this->filename));
    }
    public function testDumpContents()
    {
        $this->yaml->dump(
            array(
                'Test' => 'Test'
            )
        );
        $this->assertEquals(
            <<<YAML
Test: Test

YAML
            ,
            file_get_contents($this->filename)
        );
    }
}
