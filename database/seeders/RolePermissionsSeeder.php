<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolePermissionsSeeder extends Seeder {
	public function run(): void {
		Setting::create( [ 
			'key' => 'expiration_days',
			'value' => 90,
		] );

		$admin_role = Role::create( [ 'name' => 'admin' ] );
		$customer_role = Role::create( [ 'name' => 'customer' ] );

		$admin = User::create( [ 
			'email' => 'admin@gmail.com',
			'password' => Hash::make( '12345678' ),
		] );
		$customer = User::create( [ 
			'email' => 'customer@gmail.com',
			'password' => Hash::make( '12345678' ),
		] );
		$admin->assignRole( 'admin' );
		$customer->assignRole( 'customer' );

		$modules = [ 'type', 'manufacturer', 'distributor', 'term', 'customer', 'contract', 'renewal' ];
		$crud_actions = [ 'index', 'store', 'update', 'show', 'destroy' ];
		$permissions = [];

		foreach ( $modules as $module ) {
			foreach ( $crud_actions as $action ) {
				$permission = $module . '.' . $action;
				$p = Permission::create( [ 'name' => $permission ] );
				array_push( $permissions, $p->id );
			}
		}

		$admin_role->syncPermissions( $permissions );

		$manual = Permission::create( [ 'name' => 'home' ] );
		$admin_role->givePermissionTo( $manual->id );
		$customer_role->givePermissionTo( $manual->id );

		$common_permissions = [ 'contract.index', 'contract.store', 'contract.update', 'contract.show', 'contract.destroy' ];
		foreach ( $common_permissions as $common ) {
			$per = Permission::where( 'name', $common )->first();
			$customer_role->givePermissionTo( $per->id );
		}
	}
}
