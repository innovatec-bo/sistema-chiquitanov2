<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ResetDatabase extends Command
{
    protected $signature = 'serebo:clean-instance {--force}';
    protected $description = 'Vacía transacciones, almacenes y usuarios secundarios conservando catálogos y superadmin';

    protected array $truncateTables = [
        // Archivos, sesiones y logs
        'activity_log', 'failed_jobs', 'sessions', 'password_resets', 'personal_access_tokens',
        'sec_deleted_status_logs', 'sec_executive_summary_log', 'sys_files', 'media',
        
        // Almacenes y Bodegas
        'wfl_warehouses', 'wfl_warehouse_setup', 'wfl_warehouse_status_log',
        'external_balance_materials', 'external_balances', 'mat_internal_warehouse_operations',
        'mat_internals', 'mat_materials_summary', 'mat_projects_materials',

        // Pagos y Contratos
        'wfl_payment_orders_status_log', 'wfl_payment_orders_projects', 'wfl_payment_orders', 'wfl_contracts',
        
        // Movimientos de Construcción y Mano de obra
        'bui_blocked_log_date_ranges', 'bui_builders_in_manpower', 'bui_building_points',
        'bui_custom_structure_materials', 'bui_labor_cost', 'bui_labor_cost_log',
        'bui_labor_details', 'bui_structure_by_points', 'bui_worked_up_structures',
        'labor_cost_change_logs',
        
        // Proyectos y Obras
        'tree_prunings', 'wfl_construction_assignments', 'wfl_cre_fiscal', 'wfl_dates_to_work',
        'wfl_external_fiscal_observations', 'wfl_incidents', 'wfl_project_budgets',
        'wfl_project_points', 'wfl_project_real_budgets', 'wfl_project_stakes',
        'wfl_project_status_files', 'wfl_project_status_log', 'wfl_stakes_team_leader',
        'wfl_status_log_responsibles', 'wfl_status_responsibles', 'wfl_tracking_list',
        'wfl_user_ubmos', 'wfl_work_plan_dates', 'wfl_work_plans',
        'project_management', 'project_systems', 'sec_user_supervisor_by_period',
        'wfl_projects','workflows','wfl_workflow_column_groups','wfl_process_line','wfl_production_limits'
    ];

    public function handle(): int
    {
        if (!$this->option('force') && !$this->confirm('¿Deseas purgar todas las operaciones, almacenes y dejar solo el usuario admin?')) {
            $this->info('Operación cancelada.');
            return 0;
        }

        Schema::disableForeignKeyConstraints();

        // 1. Truncar tablas operativas y transaccionales
        foreach ($this->truncateTables as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
                $this->line("<info>Truncada:</info> {$table}");
            }
        }

        // 2. Limpieza de usuarios en Laravel (users)
        if (Schema::hasTable('users')) {
            // Asumiendo que tu usuario es el ID 1
            $preservedUserId = 1; 

            DB::table('users')->where('id', '<>', $preservedUserId)->delete();
            $this->line("<comment>Usuarios secundarios eliminados en 'users'. Conservado ID: {$preservedUserId}</comment>");

            // Limpiar roles/permisos huérfanos de Spatie/Laravel
            if (Schema::hasTable('model_has_roles')) {
                DB::table('model_has_roles')
                    ->where('model_type', 'App\\Models\\User')
                    ->where('model_id', '<>', $preservedUserId)
                    ->delete();
            }
            if (Schema::hasTable('model_has_permissions')) {
                DB::table('model_has_permissions')
                    ->where('model_type', 'App\\Models\\User')
                    ->where('model_id', '<>', $preservedUserId)
                    ->delete();
            }

            if (Schema::hasTable('users')) {
                $nextUserId = (DB::table('users')->max('id') ?? 0) + 1;
                DB::statement("ALTER TABLE users AUTO_INCREMENT = {$nextUserId}");
                $this->line("<info>AUTO_INCREMENT de 'users' ajustado a: {$nextUserId}</info>");
            }
        }

        // 3. Limpieza de usuarios en el sistema legacy (sec_users)
        if (Schema::hasTable('sec_users')) {
            $preservedSecUserId = 1; // Ajusta si en sec_users tu ID o login es específico

            DB::table('sec_users')->where('id_usr', '<>', $preservedSecUserId)->delete();
            $this->line("<comment>Usuarios secundarios eliminados en 'sec_users'. Conservado ID: {$preservedSecUserId}</comment>");

            if (Schema::hasTable('sec_userroles')) {

                $userCol = 'userid_uro';
                DB::table('sec_userroles')->where($userCol, '<>', $preservedSecUserId)->delete();
            }

            if (Schema::hasTable('sec_users')) {
                $nextSecUserId = (DB::table('sec_users')->max('id_usr') ?? 0) + 1;
                DB::statement("ALTER TABLE sec_users AUTO_INCREMENT = {$nextSecUserId}");
                $this->line("<info>AUTO_INCREMENT de 'sec_users' ajustado a: {$nextSecUserId}</info>");
            }
        }

        Schema::enableForeignKeyConstraints();

        $this->info('¡Purga completada exitosamente! Base de datos lista para pruebas locales en serebo2.');
        return 0;
    }
}