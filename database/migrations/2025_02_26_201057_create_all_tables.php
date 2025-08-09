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
            $table->longText('image')->nullable();
            $table->longText('voice')->nullable();
            $table->longText('video')->nullable();
            $table->longText('qtext')->nullable();
            $table->foreign('theme_id')->references('id')->on('themes')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('exercise_id');
            $table->longText('question');
            $table->longText('image')->nullable();
            $table->string('answer1');
            $table->string('answer2');
            $table->string('answer3');
            $table->string('answer4');
            $table->string('answer5');
            $table->enum('correct_answer', ['answer1', 'answer2', 'answer3', 'answer4', 'answer5']);
            $table->foreign('exercise_id')->references('id')->on('exercises')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('declarations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('theme_id');
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('context');
            $table->longText('image')->nullable();
            $table->longText('pdf')->nullable();
            $table->longText('video')->nullable();
            $table->longText('voice')->nullable();
            $table->longText('answerkey')->nullable();
            $table->foreign('theme_id')->references('id')->on('themes')->onDelete('cascade')->unique();
            $table->timestamps();
        });

        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title_tr');
            $table->string('title_en');
            $table->string('slogan_tr');
            $table->string('slogan_en');
            $table->longText('description_tr');
            $table->longText('description_en');
            $table->longText('image')->nullable();
            $table->timestamps();
        });

        /*         Schema::create('user_answers', function (Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('user_id');
                    $table->unsignedBigInteger('question_id');
                    $table->enum('user_answer', ['answer1', 'answer2', 'answer3', 'answer4', 'answer5']);
                    $table->timestamps();
                    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                    $table->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');
                });
                Schema::create('user_results', function (Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('user_id');
                    $table->unsignedBigInteger('exercise_id');
                    $table->integer('point');
                    $table->integer('correct_number');
                    $table->integer('wrong_number');
                    $table->timestamps();
                    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                    $table->foreign('exercise_id')->references('id')->on('exercises')->onDelete('cascade');
                }); */

        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->string('fullname');
            $table->string('email');
            $table->string('telephone');
            $table->string('subject');
            $table->string('message');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('messages');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('exercises');
        Schema::dropIfExists('declarations');
        Schema::dropIfExists('themes');
        Schema::dropIfExists('levels');
        Schema::dropIfExists('sub_levels');
        
        Schema::dropIfExists('courses');


        /*         Schema::dropIfExists('user_answers');
                Schema::dropIfExists('user_results'); */

    }
};
