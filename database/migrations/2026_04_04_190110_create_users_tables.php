<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateUsersTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(
            <<<SQL
            CREATE TABLE IF NOT EXISTS accounttype (
                id SMALLSERIAL PRIMARY KEY,
                description VARCHAR(255) NOT NULL
            );

            CREATE TABLE IF NOT EXISTS clients (
                id BIGSERIAL PRIMARY KEY,
                account_type SMALLINT NOT NULL,
                complete_name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL UNIQUE,
                cpf VARCHAR(11) UNIQUE,
                cnpj VARCHAR(14) UNIQUE,
                password VARCHAR(255) NOT NULL,
                FOREIGN KEY (account_type) REFERENCES accounttype(id) ON DELETE CASCADE ON UPDATE CASCADE
            );
SQL
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared(
            <<<SQL
            DROP TABLE IF EXISTS clients;
            DROP TABLE IF EXISTS accounttype
SQL
        );
    }
}
