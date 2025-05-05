<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tambah kolom UUID baru sementara
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->after('id');
        });

        // 2. Isi kolom UUID dengan UUID baru
        DB::table('users')->get()->each(function ($user) {
            DB::table('users')->where('id', $user->id)->update(['uuid' => (string) Str::uuid()]);
        });

        // 3. Drop FK relasi lain yang mengacu ke users (sementara, jika ada)

        // 4. Hapus primary key lama dan kolom id
        Schema::table('users', function (Blueprint $table) {
            // $table->dropPrimary('users_pkey'); // PostgreSQL
            $table->dropColumn('id');
        });

        // 5. Ubah kolom uuid jadi id baru
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('uuid', 'id');
        });

        // 6. Set primary key ke kolom UUID baru
        Schema::table('users', function (Blueprint $table) {
            $table->primary('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 1. Drop primary key dari kolom id UUID
            $table->dropPrimary('users_pkey');

            // 2. Rename kolom id kembali ke uuid
            $table->renameColumn('id', 'uuid');

            // 3. Tambah kolom id auto-increment baru
            $table->id()->first();

            // 4. Drop kolom uuid
            $table->dropColumn('uuid');
        });
    }
};
