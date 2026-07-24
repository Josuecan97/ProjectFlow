<?php

namespace Database\Seeders;

use App\Domain\Organizations\Enums\SystemRole;
use App\Domain\Organizations\Models\Permission;
use App\Domain\Organizations\Models\Role;
use Illuminate\Database\Seeder;

final class AccessControlSeeder extends Seeder
{
    /** @var array<string, string> */
    private const PERMISSIONS = [
        'organization.manage' => 'Administrar la configuración de la organización',
        'organization.transfer' => 'Transferir la propiedad de la organización',
        'members.view' => 'Consultar miembros',
        'members.invite' => 'Invitar miembros',
        'members.update' => 'Actualizar miembros y roles',
        'members.remove' => 'Retirar miembros',
        'roles.view' => 'Consultar roles y permisos',
        'roles.manage' => 'Administrar roles',
        'people.view' => 'Consultar personas',
        'people.create' => 'Crear personas',
        'people.update' => 'Actualizar personas',
        'people.archive' => 'Archivar personas',
        'quotes.view' => 'Consultar cotizaciones',
        'quotes.create' => 'Crear cotizaciones',
        'quotes.update' => 'Actualizar cotizaciones',
        'quotes.approve' => 'Aprobar cotizaciones',
        'quotes.archive' => 'Archivar cotizaciones',
        'projects.view' => 'Consultar proyectos',
        'projects.create' => 'Crear proyectos',
        'projects.update' => 'Actualizar proyectos',
        'projects.change_status' => 'Cambiar estado de proyectos',
        'stages.manage' => 'Administrar etapas',
        'actions.view' => 'Consultar acciones',
        'actions.create' => 'Crear acciones',
        'actions.update' => 'Actualizar acciones',
        'actions.complete' => 'Completar acciones',
        'activity.view_internal' => 'Consultar Bitácora interna',
        'activity.create' => 'Crear Bitácora',
        'activity.update' => 'Actualizar Bitácora',
        'activity.delete' => 'Eliminar Bitácora',
        'activity.restore' => 'Restaurar Bitácora',
        'activity.share' => 'Compartir Bitácora',
        'files.view' => 'Consultar archivos',
        'files.upload' => 'Cargar archivos',
        'files.archive' => 'Archivar archivos',
        'files.share' => 'Compartir archivos',
        'portal.manage' => 'Administrar el Portal del Cliente',
        'automations.manage' => 'Administrar automatizaciones',
        'dashboard.view' => 'Consultar el Dashboard',
        'subscription.view' => 'Consultar la membresía comercial',
        'subscription.manage' => 'Administrar manualmente membresías comerciales',
    ];

    public function run(): void
    {
        Permission::query()->where('code', 'quotes.cancel')->delete();

        $permissions = collect(self::PERMISSIONS)
            ->map(fn (string $description, string $code): Permission => Permission::query()->updateOrCreate(
                ['code' => $code],
                ['description' => $description],
            ));

        $rolePermissions = [
            SystemRole::Owner->value => $permissions->keys()->all(),
            SystemRole::Administrator->value => $permissions->keys()
                ->reject(fn (string $code): bool => in_array($code, [
                    'organization.transfer',
                    'subscription.manage',
                ], true))
                ->all(),
            SystemRole::ProjectManager->value => [
                'members.view',
                'roles.view',
                'people.view',
                'people.create',
                'people.update',
                'quotes.view',
                'projects.view',
                'projects.create',
                'projects.update',
                'projects.change_status',
                'stages.manage',
                'actions.view',
                'actions.create',
                'actions.update',
                'actions.complete',
                'activity.view_internal',
                'activity.create',
                'activity.update',
                'files.view',
                'files.upload',
                'dashboard.view',
                'subscription.view',
            ],
            SystemRole::Collaborator->value => [
                'people.view',
                'quotes.view',
                'projects.view',
                'actions.view',
                'actions.update',
                'actions.complete',
                'activity.view_internal',
                'activity.create',
                'files.view',
                'files.upload',
                'dashboard.view',
                'subscription.view',
            ],
            SystemRole::Viewer->value => [
                'people.view',
                'quotes.view',
                'projects.view',
                'actions.view',
                'activity.view_internal',
                'files.view',
                'dashboard.view',
                'subscription.view',
            ],
        ];

        foreach (SystemRole::cases() as $systemRole) {
            $role = Role::query()->updateOrCreate(
                ['code' => $systemRole->value],
                ['name' => $systemRole->label(), 'is_system' => true],
            );

            $role->permissions()->sync(
                $permissions
                    ->only($rolePermissions[$systemRole->value])
                    ->pluck('id'),
            );
        }
    }
}
