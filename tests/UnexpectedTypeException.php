<?php

namespace PhpCommon\Comparison\Tests;

use PhpCommon\Comparison\UnexpectedTypeException;
use PHPUnit_Framework_TestCase;
use Exception;

class UnexpectedTypeException extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \PhpCommon\Comparison\UnexpectedTypeException
     * @expectedExceptionMessage Expected value of type "integer", given "string".
     * @expectedExceptionCode 100
     *
     * @testdox The forType() method creates an exception with a pre-formatted message for type mismatch
     */
    public function testForTypeCreatesAnExceptionForIncomparableTypes()
    {
        $previous = new Exception();
        $exception = UnexpectedTypeException::forType('integer', 'a', 100, $previous);
        $this->assertSame($previous, $exception->getPrevious());

        throw $exception;
    }

    /**
     * @expectedException \PhpCommon\Comparison\UnexpectedTypeException
     * @expectedExceptionMessage Expected value of type "integer", given "stdClass".
     *
     * @testdox The forType() uses the fully qualified name of the class for identifying objects in the message
     */
    public function testForTypeUseTheClassNameForIdentifyingObjects()
    {
        throw UnexpectedTypeException::forType('integer', new \stdClass());
    }
}
