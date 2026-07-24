<?php

namespace App\Domain\Organizations\Enums;

enum SystemRole: string
{
    case Owner = 'owner';
    case Administrator = 'administrator';
    case ProjectManager = 'project_manager';
    case Collaborator = 'collaborator';
    case Viewer = 'viewer';

    public function label(): string
    {
        return match ($this) {
            self::Owner => 'Propietario',
            self::Administrator => 'Administrador',
            self::ProjectManager => 'Gerente de proyectos',
            self::Collaborator => 'Colaborador',
            self::Viewer => 'Consulta',
        };
    }
}
