<?php

namespace Tests\Unit;

use App\Tenant;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;
use Tests\Helpers\SetupUser;

class WardControllerTest extends TestCase
{
  use RefreshDatabase, SetupUser, WithoutMiddleware;
}