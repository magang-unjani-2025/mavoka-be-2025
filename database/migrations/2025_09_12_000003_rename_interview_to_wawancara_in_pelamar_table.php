<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::table('pelamar')->where('status_lamaran','interview')->update(['status_lamaran'=>'wawancara']);
    }

    public function down(): void
    {
        DB::table('pelamar')->where('status_lamaran','wawancara')->update(['status_lamaran'=>'interview']);
    }
};
