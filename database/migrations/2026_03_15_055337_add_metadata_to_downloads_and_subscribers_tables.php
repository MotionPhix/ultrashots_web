<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('downloads', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->unique()->after('id');
            $table->string('title')->nullable()->after('uuid');
            $table->string('brand')->nullable()->after('title');
            $table->text('description')->nullable()->after('brand');
            $table->string('category')->nullable()->after('description');
            $table->string('file_type')->nullable()->after('category');
            $table->unsignedBigInteger('file_size')->nullable()->after('file_type');
            $table->unsignedInteger('download_count')->default(0)->after('file_size');
            $table->boolean('is_featured')->default(false)->after('download_count');
            $table->boolean('is_public')->default(true)->after('is_featured');
            $table->integer('sort_order')->default(0)->after('is_public');
            $table->timestamp('published_at')->nullable()->after('sort_order');

            $table->index('category');
            $table->index('file_type');
            $table->index('is_featured');
            $table->index('is_public');
            $table->index('download_count');
        });

        Schema::table('subscribers', function (Blueprint $table) {
            $table->string('email')->nullable()->unique()->after('id');
            $table->string('token', 64)->nullable()->after('email');
            $table->boolean('subscribed')->default(false)->after('token');
            $table->timestamp('confirmed_at')->nullable()->after('subscribed');
            $table->timestamp('subscribed_at')->nullable()->after('confirmed_at');
            $table->timestamp('unsubscribed_at')->nullable()->after('subscribed_at');
            $table->json('meta')->nullable()->after('unsubscribed_at');

            $table->index('token');
            $table->index('subscribed');
        });

        DB::table('downloads')
            ->select('id', 'uuid', 'sort_order')
            ->orderBy('id')
            ->get()
            ->each(function (object $download, int $index): void {
                $updates = [];

                if ($download->uuid === null) {
                    $updates['uuid'] = (string) Str::orderedUuid();
                }

                if ($download->sort_order === null) {
                    $updates['sort_order'] = $index + 1;
                }

                if ($updates !== []) {
                    DB::table('downloads')
                        ->where('id', $download->id)
                        ->update($updates);
                }
            });

        DB::table('subscribers')
            ->whereNull('token')
            ->whereNotNull('email')
            ->update(['token' => Str::random(64)]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('downloads', function (Blueprint $table) {
            $table->dropIndex(['category']);
            $table->dropIndex(['file_type']);
            $table->dropIndex(['is_featured']);
            $table->dropIndex(['is_public']);
            $table->dropIndex(['download_count']);
            $table->dropUnique('downloads_uuid_unique');

            $table->dropColumn([
                'uuid',
                'title',
                'brand',
                'description',
                'category',
                'file_type',
                'file_size',
                'download_count',
                'is_featured',
                'is_public',
                'sort_order',
                'published_at',
            ]);
        });

        Schema::table('subscribers', function (Blueprint $table) {
            $table->dropUnique('subscribers_email_unique');
            $table->dropIndex(['token']);
            $table->dropIndex(['subscribed']);

            $table->dropColumn([
                'email',
                'token',
                'subscribed',
                'confirmed_at',
                'subscribed_at',
                'unsubscribed_at',
                'meta',
            ]);
        });
    }
};
