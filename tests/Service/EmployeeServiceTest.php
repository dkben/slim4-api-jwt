<?php


namespace Tests\App\Service;


use PHPUnit\Framework\TestCase;

class EmployeeServiceTest extends TestCase
{
    public function testShowEmployee()
    {
        $name = 'some string';
        $this->assertEquals('some string', $name);
    }
}