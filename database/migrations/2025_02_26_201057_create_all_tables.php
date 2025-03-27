<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('levels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('sub_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('themes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('level_id');
            $table->unsignedBigInteger('sub_level_id');
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('image')->nullable();
            $table->foreign('level_id')->references('id')->on('levels')->onDelete('cascade');
            $table->foreign('sub_level_id')->references('id')->on('sub_levels')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('exercises', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('theme_id');
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('description')->nullable();
            $table->foreign('theme_id')->references('id')->on('themes')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('exercises_id');
            $table->longText('question');
            $table->longText('image')->nullable();
            $table->string('answer1');
            $table->string('answer2');
            $table->string('answer3');
            $table->string('answer4');
            $table->string('answer5');
            $table->enum('correct_answer', ['answer1', 'answer2','answer3','answer4','answer5']);
            $table->foreign('exercises_id')->references('id')->on('exercises')->onDelete('cascade');
            $table->timestamps();
        });



        Schema::create('declarations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('theme_id');
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('contents')->nullable();
            $table->longText('image')->nullable();
            $table->longText('pdf')->nullable();
            $table->longText('video')->nullable();
            $table->longText('voice')->nullable();
            $table->foreign('theme_id')->references('id')->on('themes')->onDelete('cascade')->unique();
            $table->timestamps();
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->string('fullname');
            $table->string('email');
            $table->string('telephone');
            $table->string('subject');
            $table->string('message');
            $table->timestamps();
        });

        /*         Schema::create('user_answers', function (Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('user_id');
                    $table->unsignedBigInteger('question_id');
                    $table->enum('user_select', ['select1', 'select2', 'select3', 'select4', 'select5']);
                    $table->timestamps();
                    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                    $table->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');
                }); */
        /*         Schema::create('user_results', function (Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('user_id');
                    $table->unsignedBigInteger('exam_id');
                    $table->integer('point');
                    $table->integer('correct_number');
                    $table->integer('wrong_number');
                    $table->timestamps();
                    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                    $table->foreign('exam_id')->references('id')->on('exams')->onDelete('cascade');
                }); */

        /*         Schema::create('pages', function (Blueprint $table) {
                    $table->id();
                    $table->string('name', 100);
                    $table->string('slug', 100)->unique();
                    $table->string('logo_path');
                    $table->longText('content');
                    $table->integer('queue')->default(0);
                    $table->softDeletes();
                    $table->timestamps();
                }); */
        /*         Schema::create('admins', function (Blueprint $table) {
                    $table->id();
                    $table->string('name');
                    $table->string('email');
                    $table->string('password');
                    $table->timestamps();
                }); */
        /*         Schema::create('messages', function (Blueprint $table) {
                    $table->id();
                    $table->string('fullname');
                    $table->string('email');
                    $table->string('telephone');
                    $table->string('subject');
                    $table->longText('message');
                    $table->timestamps();
                }); */
    }
    public function down(): void
    {
        Schema::dropIfExists('levels');
        Schema::dropIfExists('sub_levels');
        Schema::dropIfExists('themes');
        Schema::dropIfExists('exercises');
        Schema::dropIfExists('declarations');
        Schema::dropIfExists('messages');
    }
};
