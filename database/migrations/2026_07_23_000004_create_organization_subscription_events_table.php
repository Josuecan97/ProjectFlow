<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organization_subscription_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_subscription_id');
            $table->foreign(
                'organization_subscription_id',
                'org_subscription_events_subscription_fk',
            )
                ->references('id')
                ->on('organization_subscriptions')
                ->cascadeOnDelete();
            $table->string('type', 20);
            $table->foreignId('actor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->json('metadata')->nullable();
            $table->timestamp('occurred_at');

            $table->index(
                ['organization_subscription_id', 'occurred_at'],
                'org_subscription_events_timeline_index',
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organization_subscription_events');
    }
};
