<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Settings\GeneralSettings;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PermissionsSeeder extends Seeder
{
    private array $modules = [
        'permission', 'project', 'project status', 'role', 'ticket',
        'ticket priority', 'ticket status', 'ticket type', 'user',
        'activity', 'sprint',
    ];

    private array $pluralActions = [
        'List',
    ];

    private array $singularActions = [
        'View', 'Create', 'Update', 'Delete',
    ];

    private array $extraPermissions = [
        'Manage general settings', 'Import from Jira',
        'List timesheet data', 'View timesheet dashboard',
    ];

    private string $globalRole = 'Super Admin';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create profiles
        foreach ($this->modules as $module) {
            $plural = Str::plural($module);
            $singular = $module;
            foreach ($this->pluralActions as $action) {
                Permission::query()->firstOrCreate([
                    'name' => $action.' '.$plural,
                ]);
            }
            foreach ($this->singularActions as $action) {
                Permission::query()->firstOrCreate([
                    'name' => $action.' '.$singular,
                ]);
            }
        }

        foreach ($this->extraPermissions as $permission) {
            Permission::query()->firstOrCreate([
                'name' => $permission,
            ]);
        }

        // Create default role
        $role = Role::query()->firstOrCreate([
            'name' => $this->globalRole,
        ]);
        $settings = app(GeneralSettings::class);
        $settings->default_role = $role->id;
        $settings->save();

        // Add all permissions to default role
        $role->syncPermissions(Permission::all()->pluck('name')->toArray());

        // Assign default role to first database user
        if ($user = User::query()->first()) {
            $user->syncRoles([$this->globalRole]);
        }
    }
}
