<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('beta_leads', function (Blueprint $table) {
            $table->id();
            $table->string('email')->nullable()->unique();
            $table->string('status', 20)->default('niedokonczony')->index(); // niedokonczony | kompletny | potwierdzony | wypisany
            $table->unsignedTinyInteger('last_step')->default(1);

            $table->string('business_type', 40)->nullable();
            $table->string('business_type_other', 100)->nullable();
            $table->json('integrations')->nullable();
            $table->string('missing_text', 300)->nullable();
            $table->string('spend_range', 40)->nullable();

            $table->timestamp('consent_contact_at')->nullable();
            $table->text('consent_contact_text')->nullable();
            $table->timestamp('consent_marketing_at')->nullable();
            $table->text('consent_marketing_text')->nullable();

            $table->string('utm_source', 200)->nullable();
            $table->string('utm_medium', 200)->nullable();
            $table->string('utm_campaign', 200)->nullable();
            $table->string('utm_term', 200)->nullable();
            $table->string('utm_content', 200)->nullable();
            $table->string('referrer', 500)->nullable();

            $table->string('confirm_token', 64)->nullable()->unique();
            $table->string('unsubscribe_token', 64)->nullable()->unique();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('unsubscribed_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('beta_leads');
    }
};
