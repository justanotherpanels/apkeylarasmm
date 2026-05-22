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
        Schema::table('setting', function (Blueprint $table) {
            // Rename old columns to new names
            if (Schema::hasColumn('setting', 'favicon_imagekit_url')) {
                $table->renameColumn('favicon_imagekit_url', 'favicon_path');
            }
            if (Schema::hasColumn('setting', 'logo_imagekit_url')) {
                $table->renameColumn('logo_imagekit_url', 'logo_path');
            }
            if (Schema::hasColumn('setting', 'whatsapp')) {
                $table->renameColumn('whatsapp', 'whatsapp_url');
            }

            // Drop telegram column (not needed)
            if (Schema::hasColumn('setting', 'telegram')) {
                $table->dropColumn('telegram');
            }

            // Add new text columns for head/footer code
            if (!Schema::hasColumn('setting', 'head_code')) {
                $table->text('head_code')->nullable()->after('whatsapp_url');
            }
            if (!Schema::hasColumn('setting', 'footer_code')) {
                $table->text('footer_code')->nullable()->after('head_code');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('setting', function (Blueprint $table) {
            if (Schema::hasColumn('setting', 'favicon_path')) {
                $table->renameColumn('favicon_path', 'favicon_imagekit_url');
            }
            if (Schema::hasColumn('setting', 'logo_path')) {
                $table->renameColumn('logo_path', 'logo_imagekit_url');
            }
            if (Schema::hasColumn('setting', 'whatsapp_url')) {
                $table->renameColumn('whatsapp_url', 'whatsapp');
            }
            if (!Schema::hasColumn('setting', 'telegram')) {
                $table->string('telegram')->nullable();
            }
            if (Schema::hasColumn('setting', 'head_code')) {
                $table->dropColumn('head_code');
            }
            if (Schema::hasColumn('setting', 'footer_code')) {
                $table->dropColumn('footer_code');
            }
        });
    }
};
