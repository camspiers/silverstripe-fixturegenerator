<?php

namespace Camspiers\SilverStripe\FixtureGenerator;

use Camspiers\SilverStripe\FixtureGenerator\Dumpers\DataArray;

class GeneratorTest extends \PHPUnit_Framework_TestCase
{
    protected $generator;
    public function setUp()
    {
        $this->generator = new Generator(new DataArray());
    }
    public function testGenerateBasic()
    {
        $this->assertEquals(
            array(
                'DataObject' => array(
                    1 => array(
                        'Test' => 'Test'
                    )
                )
            ),
            $this->generator->generate(
                new \DataObjectSet(
                    array(
                        new \DataObject(
                            array(
                                'ClassName' => 'DataObject',
                                'ID'        => 1,
                                'Test'      => 'Test'
                            )
                        )
                    )
                )
            )
        );
    }
    public function testGenerateHasOne()
    {
        $this->assertEquals(
            array(
                'TestHasOneDataObject' => array(
                    1 => array(
                        'Test'   => 'Test',
                        'Parent' => '=>DataObject.1'
                    )
                ),
                'DataObject'           => array(
                    1 => array(
                        'Test' => 'Test'
                    )
                )
            ),
            $this->generator->generate(
                new \DataObjectSet(
                    array(
                        new \TestHasOneDataObject(
                            array(
                                'ClassName' => 'TestHasOneDataObject',
                                'ID'        => 1,
                                'Test'      => 'Test'
                            )
                        )
                    )
                )
            )
        );
    }
    public function testGenerateHasMany()
    {
        $this->assertEquals(
            array(
                'TestHasManyDataObject' => array(
                    1 => array(
                        'Test'  => 'Test',
                        'Items' => '=>DataObject.1'
                    )
                ),
                'DataObject'            => array(
                    1 => array(
                        'Test' => 'Test'
                    )
                )
            ),
            $this->generator->generate(
                new \DataObjectSet(
                    array(
                        new \TestHasManyDataObject(
                            array(
                                'ClassName' => 'TestHasManyDataObject',
                                'ID'        => 1,
                                'Test'      => 'Test'
                            )
                        )
                    )
                )
            )
        );
    }
}
