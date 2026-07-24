<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('type', 20);
            $table->string('display_name');
            $table->string('legal_name')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('tax_id', 20)->nullable();
            $table->string('curp', 18)->nullable();
            $table->string('primary_email')->nullable();
            $table->string('primary_phone', 30)->nullable();
            $table->string('website')->nullable();
            $table->json('address')->nullable();
            $table->text('notes')->nullable();
            $table->string('status', 20)->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['organization_id', 'tax_id'], 'people_org_tax_unique');
            $table->index(['organization_id', 'status'], 'people_org_status_index');
            $table->index(['organization_id', 'type'], 'people_org_type_index');
            $table->index(['organization_id', 'display_name'], 'people_org_name_index');
            $table->index(['organization_id', 'primary_email'], 'people_org_email_index');
            $table->index(['organization_id', 'primary_phone'], 'people_org_phone_index');
        });

        Schema::create('person_roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 40)->unique();
            $table->boolean('is_system')->default(true);
            $table->timestamps();
        });

        Schema::create('person_role', function (Blueprint $table) {
            $table->foreignId('person_id')->constrained()->cascadeOnDelete();
            $table->foreignId('person_role_id')->constrained()->cascadeOnDelete();

            $table->primary(['person_id', 'person_role_id']);
        });

        Schema::create('person_relationships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_person_id')->constrained('people')->cascadeOnDelete();
            $table->foreignId('related_person_id')->constrained('people')->cascadeOnDelete();
            $table->string('type', 30)->default('contact');
            $table->string('job_title')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->unsignedBigInteger('primary_parent_person_id')
                ->nullable()
                ->storedAs('case when is_primary = 1 then parent_person_id else null end');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(
                ['parent_person_id', 'related_person_id', 'type'],
                'person_relationship_unique',
            );
            $table->unique(
                'primary_parent_person_id',
                'person_relationship_primary_unique',
            );
            $table->index(
                ['organization_id', 'parent_person_id'],
                'person_relationship_org_parent_index',
            );
            $table->index(
                ['organization_id', 'related_person_id'],
                'person_relationship_org_related_index',
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('person_relationships');
        Schema::dropIfExists('person_role');
        Schema::dropIfExists('person_roles');
        Schema::dropIfExists('people');
    }
};
