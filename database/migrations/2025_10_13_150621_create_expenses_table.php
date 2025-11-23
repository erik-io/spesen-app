<?php

declare(strict_types=1);

use App\Models\Expense;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            // Set user_id to null on user deletion to retain expense records
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('amount', Expense::AMOUNT_PRECISION, Expense::AMOUNT_SCALE);
            $table->date('expense_date');
            $table->string('cost_center', Expense::MAX_COST_CENTER_LENGTH);
            $table->enum('status', [Expense::STATUS_PENDING, Expense::STATUS_APPROVED, Expense::STATUS_REJECTED])->default(Expense::STATUS_PENDING);
            $table->text('rejection_comment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
