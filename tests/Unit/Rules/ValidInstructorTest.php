<?php

namespace Tests\Unit\Rules;

use App\Instructor;
use App\Rules\ValidInstructor;
use Tests\TestCase;

class ValidInstructorTest extends TestCase
{
  public function testItValidatesAValidInstructor()
  {
    $instructor = factory(Instructor::class)->create();
    $rule = new ValidInstructor();

    $this->assertTrue($rule->passes('', $instructor->id));
  }

  public function testItValidatesAnInvalidInstructor()
  {
    $rule = new ValidInstructor();

    $this->assertFalse($rule->passes('', 0));
  }

  public function testItHasAValidationMessage()
  {
    $rule = new ValidInstructor();

    $this->assertEquals(':attribute is an invalid instructor', $rule->message());
  }
}
