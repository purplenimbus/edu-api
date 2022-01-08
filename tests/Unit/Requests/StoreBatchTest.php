<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\StoreBatch;
use Tests\TestCase;

class StoreBatchTest extends TestCase
{
  public function testItAuthorizesRequests()
  {
    $request = new StoreBatch();

    $this->assertEquals(true, $request->authorize());
  }

  public function testItHasValidationRules()
  {
    $request = new StoreBatch();

    $this->assertEquals("required", $request->rules()["type"][0]);
    $this->assertEquals("in:\"course\",\"student\",\"instructor\",\"guardian\"", $request->rules()["type"][1]->__toString());
    $this->assertEquals("required|array", $request->rules()["data"]);
  }
}