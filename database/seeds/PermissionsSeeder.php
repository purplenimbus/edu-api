<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class PermissionsSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $roles = [
      [
        'name' => 'admin',
        'permissions' => [
          'bills' => ['create', 'view', 'edit', 'delete'],
          'courses' => ['create', 'view', 'edit', 'delete'],
          'users' => ['create', 'view', 'edit', 'delete'],
          'registrations' => ['create', 'view', 'edit', 'delete'],
          'tenants' => ['create', 'view', 'edit'],
        ],
      ],
      [
        'name' => 'alumni',
        'permissions' => [
          'bills' => ['view'],
          'users' => ['view'],
          'registrations' => ['view'],
        ],
      ],
      [
        'name' => 'other',
        'permissions' => [],
      ],
      [
        'name' => 'student',
        'permissions' => [
          'courses' => ['view'],
          'registrations' => ['view'],
        ]
      ],
      [
        'name' => 'superadmin',
        'permissions' => [
          'bills' => ['create', 'view', 'edit', 'delete'],
          'courses' => ['create', 'view', 'edit', 'delete'],
          'users' => ['create', 'view', 'edit', 'delete'],
          'registrations' => ['create', 'view', 'edit', 'delete'],
          'tenants' => ['create', 'view', 'edit', 'delete'],
        ],
      ],
      [
        'name' => 'instructor',
        'permissions' => [
          'courses' => ['view'],
          'users' => ['view'],
          'registrations' => ['view'],
        ],
      ],
      [
        'name' => 'guardian',
        'permissions' => [
          'bills' => ['view'],
          'courses' => ['view'],
          'registrations' => ['view'],
        ],
      ],
    ];

    $permissions = [
      'bills' => ['create', 'view', 'edit', 'delete'],
      'courses' => ['create', 'view', 'edit', 'delete'],
      'users' => ['create', 'view', 'edit', 'delete'],
      'registrations' => ['create', 'view', 'edit', 'delete'],
      'tenants' => ['create', 'view', 'edit', 'delete'],
    ];

    $this->create_permissions($permissions);

    $this->create_roles($roles);
  }

  private function create_roles($roles) {
    foreach ($roles as $role) {
      Bouncer::role()->firstOrCreate(
        Arr::only($role, ['name'])
      );

      if (isset($role['permissions'])) {
        foreach ($role['permissions'] as $resource_key => $permissions) {
          foreach ($permissions as $permission) {
            Bouncer::allow($role['name'])->to("{$permission}-{$resource_key}");         
          }
        }
      }
    }
  }

  private function create_permissions($permissions) {
    foreach ($permissions as $permission_key => $permissions_value) {
      foreach ($permissions_value as $permission) {
        $permission = Bouncer::ability()->firstOrCreate([
          'name' => "{$permission}-{$permission_key}"
        ]);
      }
    }
  }
}
