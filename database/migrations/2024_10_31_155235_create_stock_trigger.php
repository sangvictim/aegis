<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Buat fungsi trigger untuk mengurangi stok
        DB::unprepared('
            CREATE OR REPLACE FUNCTION kurangi_stok()
            RETURNS TRIGGER AS $$
            BEGIN
                UPDATE product_stocks
                SET stock = stock - NEW.quantity
                WHERE product_id = NEW.product_id;

                IF (SELECT stock FROM product_stocks WHERE product_id = NEW.product_id) < 0 THEN
                    RAISE EXCEPTION \'Stok produk tidak mencukupi\';
                END IF;

                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;
        ');

        // Buat fungsi trigger untuk menambah stok
        DB::unprepared('
            CREATE OR REPLACE FUNCTION tambah_stok()
            RETURNS TRIGGER AS $$
            BEGIN
                UPDATE product_stocks
                SET stock = stock + NEW.quantity
                WHERE product_id = NEW.product_id;

                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;
        ');


        // Buat trigger di tabel penjualan yang memanggil fungsi kurangi_stok setelah insert
        DB::unprepared('
            CREATE TRIGGER trigger_kurangi_stok
            AFTER INSERT ON sales_order_details
            FOR EACH ROW
            EXECUTE FUNCTION kurangi_stok();
        ');

        // Buat trigger di tabel refund yang memanggil fungsi tambah_stok setelah insert
        DB::unprepared('
            CREATE TRIGGER trigger_tambah_stok
            AFTER INSERT ON refund_details
            FOR EACH ROW
            EXECUTE FUNCTION tambah_stok();
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus trigger dan fungsi jika rollback migration
        DB::unprepared('DROP TRIGGER IF EXISTS trigger_kurangi_stok ON sales_order_details;');
        DB::unprepared('DROP FUNCTION IF EXISTS kurangi_stok();');
        DB::unprepared('DROP TRIGGER IF EXISTS trigger_tambah_stok ON refund_details;');
        DB::unprepared('DROP FUNCTION IF EXISTS tambah_stok();');
    }
};
