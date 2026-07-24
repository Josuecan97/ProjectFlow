<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organization_members', function (Blueprint $table) {
            $table->unique(['organization_id', 'id'], 'organization_members_org_id_unique');
        });

        Schema::create('quote_sequences', function (Blueprint $table) {
            $table->foreignId('organization_id')->primary()->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('last_number')->default(0);
            $table->timestamps();
        });

        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('person_id');
            $table->string('number', 10);
            $table->string('status', 20)->default('draft');
            $table->unsignedBigInteger('current_version_id')->nullable();
            $table->unsignedBigInteger('approved_version_id')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by_organization_member_id')
                ->nullable()
                ->constrained('organization_members')
                ->nullOnDelete();
            $table->timestamps();

            $table->unique(['organization_id', 'id'], 'quotes_org_id_unique');
            $table->unique(['organization_id', 'number'], 'quotes_org_number_unique');
            $table->index(['organization_id', 'status'], 'quotes_org_status_index');
            $table->index(['organization_id', 'person_id'], 'quotes_org_person_index');

            $table->foreign(
                ['organization_id', 'person_id'],
                'quotes_person_tenant_foreign',
            )->references(['organization_id', 'id'])->on('people')->cascadeOnDelete();

        });

        Schema::create('quote_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('quote_id');
            $table->unsignedInteger('version_number');
            $table->string('status', 20)->default('draft');
            $table->string('title');
            $table->text('description')->nullable();
            $table->longText('scope')->nullable();
            $table->longText('terms')->nullable();
            $table->text('notes')->nullable();
            $table->date('issued_on');
            $table->date('expires_on');
            $table->char('currency', 3);
            $table->string('client_name');
            $table->string('contact_name')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone', 30)->nullable();
            $table->json('client_address')->nullable();
            $table->decimal('subtotal', 20, 6)->default(0);
            $table->decimal('discount_total', 20, 6)->default(0);
            $table->decimal('tax_total', 20, 6)->default(0);
            $table->decimal('total', 20, 6)->default(0);
            $table->foreignId('created_by_organization_member_id')
                ->nullable()
                ->constrained('organization_members')
                ->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->unique(
                ['organization_id', 'quote_id', 'id'],
                'quote_versions_org_quote_id_unique',
            );
            $table->unique(['organization_id', 'id'], 'quote_versions_org_id_unique');
            $table->unique(['quote_id', 'version_number'], 'quote_versions_number_unique');
            $table->index(['organization_id', 'status'], 'quote_versions_org_status_index');

            $table->foreign(
                ['organization_id', 'quote_id'],
                'quote_versions_quote_tenant_foreign',
            )->references(['organization_id', 'id'])->on('quotes')->cascadeOnDelete();

        });

        Schema::create('quote_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('quote_version_id');
            $table->unsignedInteger('position');
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('quantity', 16, 4);
            $table->string('unit', 40);
            $table->decimal('unit_price', 20, 6);
            $table->decimal('discount_amount', 20, 6)->default(0);
            $table->decimal('tax_rate', 7, 4)->default(0);
            $table->decimal('subtotal', 20, 6);
            $table->decimal('tax_amount', 20, 6);
            $table->decimal('total', 20, 6);
            $table->timestamps();

            $table->unique(
                ['quote_version_id', 'position'],
                'quote_items_version_position_unique',
            );
            $table->index(
                ['organization_id', 'quote_version_id'],
                'quote_items_org_version_index',
            );

            $table->foreign(
                ['organization_id', 'quote_version_id'],
                'quote_items_version_tenant_foreign',
            )->references(['organization_id', 'id'])->on('quote_versions')->cascadeOnDelete();
        });

        Schema::create('quote_version_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('quote_version_id');
            $table->unsignedBigInteger('changed_by_organization_member_id')->nullable();
            $table->string('type', 40);
            $table->json('before_values');
            $table->json('after_values');
            $table->timestamp('created_at')->useCurrent();

            $table->index(
                ['organization_id', 'quote_version_id', 'created_at'],
                'quote_revisions_org_version_created_index',
            );

            $table->foreign(
                ['organization_id', 'quote_version_id'],
                'quote_revisions_version_tenant_foreign',
            )->references(['organization_id', 'id'])->on('quote_versions')->cascadeOnDelete();

            $table->foreign(
                'changed_by_organization_member_id',
                'quote_revisions_member_foreign',
            )->references('id')->on('organization_members')->nullOnDelete();

        });

        Schema::table('quotes', function (Blueprint $table) {
            $table->foreign(
                'current_version_id',
                'quotes_current_version_foreign',
            )->references('id')->on('quote_versions')->nullOnDelete();

            $table->foreign(
                'approved_version_id',
                'quotes_approved_version_foreign',
            )->references('id')->on('quote_versions')->nullOnDelete();
        });

        $this->addQuoteVersionPointerTriggers();
        $this->addOrganizationMemberTenantTriggers();
        $this->addQuoteItemChecks();
    }

    public function down(): void
    {
        $this->dropQuoteItemChecks();
        $this->dropOrganizationMemberTenantTriggers();
        $this->dropQuoteVersionPointerTriggers();

        Schema::table('quotes', function (Blueprint $table) {
            $table->dropForeign('quotes_current_version_foreign');
            $table->dropForeign('quotes_approved_version_foreign');
        });

        Schema::dropIfExists('quote_version_revisions');
        Schema::dropIfExists('quote_items');
        Schema::dropIfExists('quote_versions');
        Schema::dropIfExists('quotes');
        Schema::dropIfExists('quote_sequences');

        Schema::table('organization_members', function (Blueprint $table) {
            $table->dropUnique('organization_members_org_id_unique');
        });
    }

    private function addQuoteItemChecks(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement(
                'ALTER TABLE quote_items
                ADD CONSTRAINT quote_items_values_check
                CHECK (
                    quantity > 0
                    AND unit_price >= 0
                    AND discount_amount >= 0
                    AND discount_amount <= quantity * unit_price
                    AND tax_rate >= 0
                    AND tax_rate <= 100
                    AND subtotal >= 0
                    AND tax_amount >= 0
                    AND total >= 0
                )',
            );

            return;
        }

        if (DB::getDriverName() === 'sqlite') {
            foreach (['insert' => 'NEW', 'update' => 'NEW'] as $operation => $row) {
                DB::statement(
                    "CREATE TRIGGER quote_items_values_{$operation}
                    BEFORE ".strtoupper($operation)." ON quote_items
                    WHEN NOT (
                        {$row}.quantity > 0
                        AND {$row}.unit_price >= 0
                        AND {$row}.discount_amount >= 0
                        AND {$row}.discount_amount <= {$row}.quantity * {$row}.unit_price
                        AND {$row}.tax_rate >= 0
                        AND {$row}.tax_rate <= 100
                        AND {$row}.subtotal >= 0
                        AND {$row}.tax_amount >= 0
                        AND {$row}.total >= 0
                    )
                    BEGIN
                        SELECT RAISE(ABORT, 'Invalid quote item values');
                    END",
                );
            }
        }
    }

    private function addQuoteVersionPointerTriggers(): void
    {
        foreach (['insert', 'update'] as $operation) {
            $trigger = "quotes_version_pointer_{$operation}";
            $condition = <<<'SQL'
                (
                    NEW.current_version_id IS NOT NULL
                    AND NOT EXISTS (
                        SELECT 1
                        FROM quote_versions
                        WHERE id = NEW.current_version_id
                          AND quote_id = NEW.id
                          AND organization_id = NEW.organization_id
                    )
                )
                OR
                (
                    NEW.approved_version_id IS NOT NULL
                    AND NOT EXISTS (
                        SELECT 1
                        FROM quote_versions
                        WHERE id = NEW.approved_version_id
                          AND quote_id = NEW.id
                          AND organization_id = NEW.organization_id
                    )
                )
                SQL;

            if (DB::getDriverName() === 'mysql') {
                DB::unprepared(
                    "CREATE TRIGGER {$trigger}
                    BEFORE ".strtoupper($operation)." ON quotes
                    FOR EACH ROW
                    BEGIN
                        IF {$condition}
                        THEN
                            SIGNAL SQLSTATE '45000'
                                SET MESSAGE_TEXT = 'Quote version belongs to another quote or tenant';
                        END IF;
                    END",
                );
            }

            if (DB::getDriverName() === 'sqlite') {
                DB::statement(
                    "CREATE TRIGGER {$trigger}
                    BEFORE ".strtoupper($operation)." ON quotes
                    WHEN {$condition}
                    BEGIN
                        SELECT RAISE(ABORT, 'Quote version belongs to another quote or tenant');
                    END",
                );
            }
        }
    }

    private function dropQuoteVersionPointerTriggers(): void
    {
        DB::statement('DROP TRIGGER IF EXISTS quotes_version_pointer_insert');
        DB::statement('DROP TRIGGER IF EXISTS quotes_version_pointer_update');
    }

    private function addOrganizationMemberTenantTriggers(): void
    {
        $references = [
            'quotes' => 'approved_by_organization_member_id',
            'quote_versions' => 'created_by_organization_member_id',
            'quote_version_revisions' => 'changed_by_organization_member_id',
        ];

        foreach ($references as $table => $column) {
            foreach (['insert', 'update'] as $operation) {
                $trigger = "{$table}_member_tenant_{$operation}";

                if (DB::getDriverName() === 'mysql') {
                    DB::unprepared(
                        "CREATE TRIGGER {$trigger}
                        BEFORE ".strtoupper($operation)." ON {$table}
                        FOR EACH ROW
                        BEGIN
                            IF NEW.{$column} IS NOT NULL
                                AND NOT EXISTS (
                                    SELECT 1
                                    FROM organization_members
                                    WHERE id = NEW.{$column}
                                      AND organization_id = NEW.organization_id
                                )
                            THEN
                                SIGNAL SQLSTATE '45000'
                                    SET MESSAGE_TEXT = 'Organization member belongs to another tenant';
                            END IF;
                        END",
                    );
                }

                if (DB::getDriverName() === 'sqlite') {
                    DB::statement(
                        "CREATE TRIGGER {$trigger}
                        BEFORE ".strtoupper($operation)." ON {$table}
                        WHEN NEW.{$column} IS NOT NULL
                            AND NOT EXISTS (
                                SELECT 1
                                FROM organization_members
                                WHERE id = NEW.{$column}
                                  AND organization_id = NEW.organization_id
                            )
                        BEGIN
                            SELECT RAISE(ABORT, 'Organization member belongs to another tenant');
                        END",
                    );
                }
            }
        }
    }

    private function dropOrganizationMemberTenantTriggers(): void
    {
        foreach ([
            'quotes',
            'quote_versions',
            'quote_version_revisions',
        ] as $table) {
            foreach (['insert', 'update'] as $operation) {
                DB::statement("DROP TRIGGER IF EXISTS {$table}_member_tenant_{$operation}");
            }
        }
    }

    private function dropQuoteItemChecks(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE quote_items DROP CONSTRAINT quote_items_values_check');

            return;
        }

        if (DB::getDriverName() === 'sqlite') {
            DB::statement('DROP TRIGGER IF EXISTS quote_items_values_insert');
            DB::statement('DROP TRIGGER IF EXISTS quote_items_values_update');
        }
    }
};
