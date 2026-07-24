<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('people', function (Blueprint $table) {
            $table->unique(
                ['organization_id', 'id'],
                'people_org_id_unique',
            );
        });

        Schema::table('person_relationships', function (Blueprint $table) {
            $table->dropForeign(['parent_person_id']);
            $table->dropForeign(['related_person_id']);

            $table->foreign(
                ['organization_id', 'parent_person_id'],
                'person_relationship_parent_tenant_foreign',
            )->references(['organization_id', 'id'])->on('people')->cascadeOnDelete();

            $table->foreign(
                ['organization_id', 'related_person_id'],
                'person_relationship_related_tenant_foreign',
            )->references(['organization_id', 'id'])->on('people')->cascadeOnDelete();
        });

        $this->addDistinctPeopleConstraint();
    }

    public function down(): void
    {
        $this->dropDistinctPeopleConstraint();

        Schema::table('person_relationships', function (Blueprint $table) {
            $table->dropForeign('person_relationship_parent_tenant_foreign');
            $table->dropForeign('person_relationship_related_tenant_foreign');

            $table->foreign('parent_person_id')
                ->references('id')
                ->on('people')
                ->cascadeOnDelete();
            $table->foreign('related_person_id')
                ->references('id')
                ->on('people')
                ->cascadeOnDelete();
        });

        Schema::table('people', function (Blueprint $table) {
            $table->dropUnique('people_org_id_unique');
        });
    }

    private function addDistinctPeopleConstraint(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement(
                'ALTER TABLE person_relationships
                ADD CONSTRAINT person_relationship_distinct_people_check
                CHECK (parent_person_id <> related_person_id)',
            );

            return;
        }

        if (DB::getDriverName() === 'sqlite') {
            DB::statement(
                'CREATE TRIGGER person_relationship_distinct_people_insert
                BEFORE INSERT ON person_relationships
                WHEN NEW.parent_person_id = NEW.related_person_id
                BEGIN
                    SELECT RAISE(ABORT, "A person cannot be related to itself");
                END',
            );
            DB::statement(
                'CREATE TRIGGER person_relationship_distinct_people_update
                BEFORE UPDATE OF parent_person_id, related_person_id ON person_relationships
                WHEN NEW.parent_person_id = NEW.related_person_id
                BEGIN
                    SELECT RAISE(ABORT, "A person cannot be related to itself");
                END',
            );
        }
    }

    private function dropDistinctPeopleConstraint(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement(
                'ALTER TABLE person_relationships
                DROP CONSTRAINT person_relationship_distinct_people_check',
            );

            return;
        }

        if (DB::getDriverName() === 'sqlite') {
            DB::statement('DROP TRIGGER IF EXISTS person_relationship_distinct_people_insert');
            DB::statement('DROP TRIGGER IF EXISTS person_relationship_distinct_people_update');
        }
    }
};
