<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('orders')) {
            return;
        }

        $dbName = DB::getDatabaseName();

        // created_at
        if (! $this->indexExists($dbName, 'orders', 'orders_created_at_index')) {
            DB::statement('CREATE INDEX `orders_created_at_index` ON `orders` (`created_at`)');
        }

        // status
        if (! $this->indexExists($dbName, 'orders', 'orders_status_index')) {
            DB::statement('CREATE INDEX `orders_status_index` ON `orders` (`status`)');
        }

        // city_id + type
        if (! $this->indexExists($dbName, 'orders', 'orders_city_id_type_index')) {
            DB::statement('CREATE INDEX `orders_city_id_type_index` ON `orders` (`city_id`, `type`)');
        }

        // payment_method
        if (! $this->indexExists($dbName, 'orders', 'orders_payment_method_index')) {
            DB::statement('CREATE INDEX `orders_payment_method_index` ON `orders` (`payment_method`)');
        }

        // client_id
        if (! $this->indexExists($dbName, 'orders', 'orders_client_id_index')) {
            DB::statement('CREATE INDEX `orders_client_id_index` ON `orders` (`client_id`)');
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('orders')) {
            return;
        }

        $dbName = DB::getDatabaseName();

        $this->dropIndexIfExists($dbName, 'orders', 'orders_created_at_index');
        $this->dropIndexIfExists($dbName, 'orders', 'orders_status_index');
        $this->dropIndexIfExists($dbName, 'orders', 'orders_city_id_type_index');
        $this->dropIndexIfExists($dbName, 'orders', 'orders_payment_method_index');
        $this->dropIndexIfExists($dbName, 'orders', 'orders_client_id_index');
    }

    private function indexExists(string $dbName, string $table, string $index): bool
    {
        return DB::table('information_schema.statistics')
            ->where('table_schema', $dbName)
            ->where('table_name', $table)
            ->where('index_name', $index)
            ->exists();
    }

    private function dropIndexIfExists(string $dbName, string $table, string $index): void
    {
        if ($this->indexExists($dbName, $table, $index)) {
            DB::statement("DROP INDEX `{$index}` ON `{$table}`");
        }
    }
};
