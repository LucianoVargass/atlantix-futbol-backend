#!/bin/bash

set -e

echo "===> Creando carpetas de services"
mkdir -p app/Services/Auth
mkdir -p app/Services/Admin
mkdir -p app/Services/Tournaments
mkdir -p app/Services/Teams
mkdir -p app/Services/Players
mkdir -p app/Services/Matches
mkdir -p app/Services/Payments
mkdir -p app/Services/Rules
mkdir -p app/Services/Stats

echo "===> Creando models + migrations + controllers API"

php artisan make:model GlobalRole -m
php artisan make:model Tournament -m
php artisan make:model TournamentCategory -m
php artisan make:model TournamentAdmin -m
php artisan make:model Team -m
php artisan make:model TeamAdmin -m
php artisan make:model TeamTournamentRegistration -m
php artisan make:model Player -m
php artisan make:model TeamPlayer -m
php artisan make:model TournamentPlayer -m
php artisan make:model PlayerDocument -m
php artisan make:model RulesVersion -m
php artisan make:model RuleAcceptance -m
php artisan make:model TournamentPhase -m
php artisan make:model TournamentGroup -m
php artisan make:model PhaseTeam -m
php artisan make:model Matchday -m
php artisan make:model Referee -m
php artisan make:model FootballMatch -m
php artisan make:model MatchEvent -m
php artisan make:model MatchLineup -m
php artisan make:model MatchLineupPlayer -m
php artisan make:model Payment -m
php artisan make:model PaymentReceipt -m
php artisan make:model PaymentLog -m
php artisan make:model PlayerStat -m
php artisan make:model TeamStat -m
php artisan make:model Sanction -m
php artisan make:model TournamentNews -m
php artisan make:model TournamentSetting -m

php artisan make:controller Api/GlobalRoleController --api
php artisan make:controller Api/UserController --api
php artisan make:controller Api/TournamentController --api
php artisan make:controller Api/TournamentCategoryController --api
php artisan make:controller Api/TournamentAdminController --api
php artisan make:controller Api/TeamController --api
php artisan make:controller Api/TeamAdminController --api
php artisan make:controller Api/TeamTournamentRegistrationController --api
php artisan make:controller Api/PlayerController --api
php artisan make:controller Api/TeamPlayerController --api
php artisan make:controller Api/TournamentPlayerController --api
php artisan make:controller Api/PlayerDocumentController --api
php artisan make:controller Api/RulesVersionController --api
php artisan make:controller Api/RuleAcceptanceController --api
php artisan make:controller Api/TournamentPhaseController --api
php artisan make:controller Api/TournamentGroupController --api
php artisan make:controller Api/PhaseTeamController --api
php artisan make:controller Api/MatchdayController --api
php artisan make:controller Api/RefereeController --api
php artisan make:controller Api/FootballMatchController --api
php artisan make:controller Api/MatchEventController --api
php artisan make:controller Api/MatchLineupController --api
php artisan make:controller Api/MatchLineupPlayerController --api
php artisan make:controller Api/PaymentController --api
php artisan make:controller Api/PaymentReceiptController --api
php artisan make:controller Api/PaymentLogController --api
php artisan make:controller Api/PlayerStatController --api
php artisan make:controller Api/TeamStatController --api
php artisan make:controller Api/SanctionController --api
php artisan make:controller Api/TournamentNewsController --api
php artisan make:controller Api/TournamentSettingController --api

echo "===> Creando services"

cat > app/Services/Admin/GlobalRoleService.php <<'PHP'
<?php

namespace App\Services\Admin;

use App\Models\GlobalRole;

class GlobalRoleService
{
    public function list(array $filters = [])
    {
        return GlobalRole::query()->paginate($filters['per_page'] ?? 15);
    }

    public function create(array $data): GlobalRole
    {
        return GlobalRole::create($data);
    }

    public function update(GlobalRole $globalRole, array $data): GlobalRole
    {
        $globalRole->update($data);
        return $globalRole->refresh();
    }

    public function delete(GlobalRole $globalRole): bool
    {
        return (bool) $globalRole->delete();
    }
}
PHP

cat > app/Services/Admin/UserService.php <<'PHP'
<?php

namespace App\Services\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function list(array $filters = [])
    {
        return User::query()
            ->when($filters['search'] ?? null, function ($q, $search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('dni', 'like', "%{$search}%");
                });
            })
            ->paginate($filters['per_page'] ?? 15);
    }

    public function create(array $data): User
    {
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        return User::create($data);
    }

    public function update(User $user, array $data): User
    {
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);
        return $user->refresh();
    }

    public function delete(User $user): bool
    {
        return (bool) $user->delete();
    }
}
PHP

create_service() {
  local path=$1
  local class=$2
  local model=$3

  cat > "$path" <<PHP
<?php

namespace App\\Services\\${class%/*};

use App\\Models\\${model};

class ${class##*/}
{
    public function list(array \$filters = [])
    {
        return ${model}::query()->paginate(\$filters['per_page'] ?? 15);
    }

    public function create(array \$data): ${model}
    {
        return ${model}::create(\$data);
    }

    public function update(${model} \$model, array \$data): ${model}
    {
        \$model->update(\$data);
        return \$model->refresh();
    }

    public function delete(${model} \$model): bool
    {
        return (bool) \$model->delete();
    }
}
PHP
}

create_service "app/Services/Tournaments/TournamentService.php" "Tournaments/TournamentService" "Tournament"
create_service "app/Services/Tournaments/TournamentCategoryService.php" "Tournaments/TournamentCategoryService" "TournamentCategory"
create_service "app/Services/Tournaments/TournamentAdminService.php" "Tournaments/TournamentAdminService" "TournamentAdmin"
create_service "app/Services/Teams/TeamService.php" "Teams/TeamService" "Team"
create_service "app/Services/Teams/TeamAdminService.php" "Teams/TeamAdminService" "TeamAdmin"
create_service "app/Services/Teams/TeamTournamentRegistrationService.php" "Teams/TeamTournamentRegistrationService" "TeamTournamentRegistration"
create_service "app/Services/Players/PlayerService.php" "Players/PlayerService" "Player"
create_service "app/Services/Players/TeamPlayerService.php" "Players/TeamPlayerService" "TeamPlayer"
create_service "app/Services/Players/TournamentPlayerService.php" "Players/TournamentPlayerService" "TournamentPlayer"
create_service "app/Services/Players/PlayerDocumentService.php" "Players/PlayerDocumentService" "PlayerDocument"
create_service "app/Services/Rules/RulesVersionService.php" "Rules/RulesVersionService" "RulesVersion"
create_service "app/Services/Rules/RuleAcceptanceService.php" "Rules/RuleAcceptanceService" "RuleAcceptance"
create_service "app/Services/Tournaments/TournamentPhaseService.php" "Tournaments/TournamentPhaseService" "TournamentPhase"
create_service "app/Services/Tournaments/TournamentGroupService.php" "Tournaments/TournamentGroupService" "TournamentGroup"
create_service "app/Services/Tournaments/PhaseTeamService.php" "Tournaments/PhaseTeamService" "PhaseTeam"
create_service "app/Services/Matches/MatchdayService.php" "Matches/MatchdayService" "Matchday"
create_service "app/Services/Matches/RefereeService.php" "Matches/RefereeService" "Referee"
create_service "app/Services/Matches/FootballMatchService.php" "Matches/FootballMatchService" "FootballMatch"
create_service "app/Services/Matches/MatchEventService.php" "Matches/MatchEventService" "MatchEvent"
create_service "app/Services/Matches/MatchLineupService.php" "Matches/MatchLineupService" "MatchLineup"
create_service "app/Services/Matches/MatchLineupPlayerService.php" "Matches/MatchLineupPlayerService" "MatchLineupPlayer"
create_service "app/Services/Payments/PaymentService.php" "Payments/PaymentService" "Payment"
create_service "app/Services/Payments/PaymentReceiptService.php" "Payments/PaymentReceiptService" "PaymentReceipt"
create_service "app/Services/Payments/PaymentLogService.php" "Payments/PaymentLogService" "PaymentLog"
create_service "app/Services/Stats/PlayerStatService.php" "Stats/PlayerStatService" "PlayerStat"
create_service "app/Services/Stats/TeamStatService.php" "Stats/TeamStatService" "TeamStat"
create_service "app/Services/Stats/SanctionService.php" "Stats/SanctionService" "Sanction"
create_service "app/Services/Tournaments/TournamentNewsService.php" "Tournaments/TournamentNewsService" "TournamentNews"
create_service "app/Services/Tournaments/TournamentSettingService.php" "Tournaments/TournamentSettingService" "TournamentSetting"

echo "===> Scaffold terminado"