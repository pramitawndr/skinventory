<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_reports', function (Blueprint $table) {
            $table->id();

            $table->foreignId('batch_id')
                ->constrained('batches')
                ->cascadeOnDelete();

            // Pengguna yang mengajukan transaksi
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Pengguna yang menyetujui atau menolak
            $table->foreignId('reviewed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Menghubungkan pengajuan dengan transaksi final
            $table->foreignId('stock_transaction_id')
                ->nullable()
                ->constrained('stock_transactions')
                ->nullOnDelete();

            $table->enum('type', ['masuk', 'keluar']);
            $table->unsignedInteger('quantity');

            $table->string('status')->default('pending');

            $table->text('notes')->nullable();
            $table->text('reviewer_note')->nullable();

            $table->timestamp('reviewed_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_reports');
    }
};