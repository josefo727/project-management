<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->foreignId('creator_id')->nullable()->after('owner_id')->constrained('users');
        });
        if (Schema::hasColumn('tickets', 'creator_id')) {
            DB::table('tickets')->whereNull('creator_id')->update([
                'creator_id' => DB::raw('owner_id')
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropConstrainedForeignId('creator_id');
        });
    }
};
