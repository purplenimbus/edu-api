<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\UpdateTenantSetting;
use Tests\TestCase;

class UpdateTenantSettingTest extends TestCase
{
  public function testItAuthorizesRequests()
  {
    $request = new UpdateTenantSetting();

    $this->assertEquals(true, $request->authorize());
  }

  public function testItHasDefaultValidationRules()
  {
    $request = new UpdateTenantSetting();

    $this->assertEquals("integer|required|exists:tenants,id", $request->rules()["id"]);
    $this->assertEquals("in:\"course_schema\"", $request->rules()["name"]->__toString());
  }

  public function testItHasDefaultValidationRulesForCourseSchema()
  {
    $request = new UpdateTenantSetting();
    request()->merge([
      "name" => "course_schema"
    ]);

    $this->assertEquals("required|string|max:255", $request->rules()["value.*.name"]);
    $this->assertEquals(["required", "integer", "max:100"], $request->rules()["value.*.score"]);
    $this->assertContains("required", $request->rules()["value"]);
    $this->assertContains("array", $request->rules()["value"]);
    $this->assertEquals("App\Rules\ValidScores", get_class($request->rules()["value"][2]));
    $this->assertEquals("in:\"course_schema\"", $request->rules()["name"]->__toString());
  }
}