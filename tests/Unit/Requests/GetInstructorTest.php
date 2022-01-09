<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\GetInstructor;
use App\Rules\ValidInstructor;
use Tests\TestCase;

class GetInstructorTest extends TestCase
{
  public function testItAuthorizesRequests()
  {
    $request = new GetInstructor();

    $this->assertEquals(true, $request->authorize());
  }

  public function testItHasValidationRules()
  {
    $request = new GetInstructor();

    $this->assertEquals("exists:users,id", $request->rules()["id"][0]);
    $this->assertEquals(ValidInstructor::class, get_class($request->rules()["id"][1]));
  }
}
