<?php

namespace Tests\Unit\Requests;

use App\Rules\ValidCourseSchema;
use Tests\TestCase;

class ValidCourseSchemaTest extends TestCase
{
  public function testItValidatesAValidCourseSchema()
  {
    $rule = new ValidCourseSchema();

    $this->assertTrue($rule->passes('', [
      [
        "name" => "assignment 1",
        "score" => 10,
      ],
      [
        "name" => "exam",
        "score" => 90,
      ]
    ]));
  }

  public function testItValidatesAnInvalidInstructor()
  {
    $rule = new ValidCourseSchema();

    $this->assertFalse($rule->passes('', [
      [
        "name" => "assignment 1",
        "score" => 10,
      ],
      [
        "name" => "assignment 2",
        "score" => 10,
      ],
      [
        "name" => "exam",
        "score" => 90,
      ]
    ]));
  }

  public function testItHasAValidationMessage()
  {
    $rule = new ValidCourseSchema();

    $this->assertEquals("The sum of the course scores must be 100", $rule->message());
  }
}
