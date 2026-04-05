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
            CREATE TABLE IF NOT EXISTS accounttypes (
                accounttypes_id SMALLSERIAL PRIMARY KEY,
                accounttypes_description VARCHAR(255) NOT NULL
            );

            CREATE TABLE IF NOT EXISTS juristicaccount (
                juristicaccount_id BIGSERIAL PRIMARY KEY,
                juristicaccount_accounttype SMALLINT NOT NULL,
                juristicaccount_user SMALLINT NOT NULL,
                juristicaccount_completename VARCHAR(255) NOT NULL,
                juristicaccount_email VARCHAR(255) NOT NULL UNIQUE,
                juristicaccount_cnpj VARCHAR(14) UNIQUE,
                FOREIGN KEY (juristicaccount_accounttype) REFERENCES accounttypes(accounttypes_id)
                ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (juristicaccount_user) REFERENCES users(id)
                ON DELETE CASCADE ON UPDATE CASCADE
            );

            CREATE TABLE IF NOT EXISTS fisicaccount (
                fisicaccount_id BIGSERIAL PRIMARY KEY,
                fisicaccount_accounttype SMALLINT NOT NULL,
                fisicaccount_user SMALLINT NOT NULL,
                fisicaccount_completename VARCHAR(255) NOT NULL,
                fisicaccount_email VARCHAR(255) NOT NULL UNIQUE,
                fisicaccount_cpf VARCHAR(11) UNIQUE,
                FOREIGN KEY (fisicaccount_accounttype) REFERENCES accounttypes(accounttypes_id)
                ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (fisicaccount_user) REFERENCES users(id)
                ON DELETE CASCADE ON UPDATE CASCADE
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
            DROP TABLE IF EXISTS juristicaccount;
            DROP TABLE IF EXISTS fisicaccount;
            DROP TABLE IF EXISTS accounttypes
SQL
        );
    }
}
