<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

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
      ['name' => 'admin'],
    	['name' => 'alumni'],
    	['name' => 'other'],
    	['name' => 'student'],
    	['name' => 'superadmin'],
      ['name' => 'instructor'],
    	['name' => 'parent'],
    ];

    $this->create_roles($roles);

    $permissions = [
    	'bills' => ['view','edit','delete'],
    	'courses' => ['view','edit','delete'],
    	'users' => ['view','edit','delete'],
      'registrations' => ['view','edit','delete'],
    	'tenants' => ['view','edit','delete'],
		];

    $this->create_permissions($permissions);

    $permission_mappings = [
      'student' => [
        'courses' => ['view'],
        'registrations' => ['view'],
      ],
      'instructor' => [
        'courses' => ['view', 'edit'],
        'users' => ['view'],
        'registrations' => ['view'],
      ],
      'parent' => [
        'bills' => ['view'],
        'courses' => ['view'],
        'registrations' => ['view'],
      ],
      'admin' => [
        'bills' => ['view', 'edit', 'delete'],
        'courses' => ['view', 'edit', 'delete'],
        'users' => ['view', 'edit', 'delete'],
        'registrations' => ['view', 'edit', 'delete'],
        'tenants' => ['view', 'edit'],
      ],
      'superadmin' => [
        'bills' => ['view', 'edit', 'delete'],
        'courses' => ['view', 'edit', 'delete'],
        'users' => ['view', 'edit', 'delete'],
        'registrations' => ['view', 'edit', 'delete'],
        'tenants' => ['view', 'edit', 'delete'],
      ],
      'alumni' => [
        'bills' => ['view'],
        'users' => ['view'],
        'registrations' => ['view'],
      ],
    ];

    $this->map_roles($permission_mappings);
  }

  private function create_roles($roles) {
    foreach ($roles as $role) {
      $role = Role::create($role);
    }
  }

  private function map_roles($permission_mappings) {
    foreach ($permission_mappings as $role_name => $resources) {
      $role = Role::whereName($role_name)->first();
      foreach ($resources as $resource_key => $permissions) {
        foreach ($permissions as $permission) {          
          $permission = Permission::whereName("{$permission} {$resource_key}")->first();
          if($permission){
            $role->givePermissionTo($permission);
          }

        }
      }
    }
  }

  private function create_permissions($permissions) {
    foreach ($permissions as $permission_key => $permissions_value) {
      foreach ($permissions_value as $permission) {
        $permission = Permission::create([
          'name' => "{$permission} {$permission_key}"
        ]);
      }
    }
  }
}
