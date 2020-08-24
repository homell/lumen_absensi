<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableDatamahasiswa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('datamahasiswa', function (Blueprint $table) {
            $table->integer('nomor', 8);
            $table->string('nrp', 20);
            $table->string('nama', 100);
            $table->integer('kelas');
            $table->integer('dosen_wali');
            $table->char('status', 1);
            $table->date('tgllahir');
            $table->string('tmplahir', 100);
            $table->date('tglmasuk');
            $table->char('dan_lain_lain', 1)->nullable();
            $table->char('jenis_kelamin', 1);
            $table->string('warga', 10);
            $table->integer('agama');
            $table->string('alamat', 500);
            $table->string('notelp', 100);
            $table->string('smu', 50);
            $table->string('beasiswa', 30)->nullable();
            $table->string('ayah', 60);
            $table->string('kerja_ayah', 100);
            $table->string('ibu', 60);
            $table->string('kerja_ibu', 100);
            $table->integer('penghasilan');
            $table->string('alamat_ortu', 500);
            $table->string('notelp_ortu', 100);
            $table->string('darah', 10)->nullable();
            $table->integer('nijazah');
            $table->double('nun');
            $table->string('password', 20);
            $table->date('tgllulus');
            $table->integer('lulussmu');
            $table->string('no_bni', 16);
            $table->integer('anak_ke');
            $table->string('id_asuransi', 20)->nullable();
            $table->string('alamat_smu', 500);
            $table->integer('penghasilan_ibu');
            $table->integer('jumlah_anak');
            $table->string('keterangan_ayah', 50);
            $table->string('keterangan_ibu', 50);
            $table->string('prestasi_olahraga', 100)->nullable();
            $table->string('tempat_kerja', 100)->nullable();
            $table->string('gaji_kerja', 20)->nullable();
            $table->string('jabatan_kerja', 100)->nullable();
            $table->integer('hak')->nullable()->default(0);
            $table->integer('el')->default(0)->nullable();
            $table->string('no_pendaftaran', 20)->nullable();
            $table->integer('jalur_daftar')->nullable();
            $table->string('nisn', 50)->nullable();
            $table->string('npsn', 50)->nullable();
            $table->string('spp1', 75)->nullable();
            $table->string('spp2', 75)->nullable();
            $table->string('spp3', 75)->nullable();
            $table->string('spp4', 75)->nullable();
            $table->string('spp5', 75)->nullable();
            $table->string('spp6', 75)->nullable();
            $table->string('spp7', 75)->nullable();
            $table->string('spp8', 75)->nullable();
            $table->integer('angkatan')->nullable();
            $table->integer('semester_masuk')->nullable();
            $table->integer('mahasiswa_jalur_penerimaan')->nullable();
            $table->string('nik', 50)->nullable();
            $table->string('kota_ortu', 20)->nullable();
            $table->string('alamat_kota', 20)->nullable();
            $table->string('subkampus', 20)->nullable();
            $table->integer('ukt')->nullable();
            $table->integer('sekolah')->nullable();
            $table->string('foto')->nullable();
            $table->string('ijazah')->nullable();
            $table->integer('status_kawin')->nullable();
            $table->string('ukuran_baju', 5)->nullable();
            $table->integer('pernahpt')->nullable();
            $table->integer('tahunmasuk_pt')->nullable();
            $table->integer('jumlah_sks')->nullable();
            $table->string('pt_asal', 200)->nullable();
            $table->integer('nunmapel')->nullable();
            $table->integer('nijazahmapel')->nullable();
            $table->integer('status_smu')->nullable();
            $table->integer('jurusan_smu')->nullable();
            $table->integer('thlahirayah')->nullable();
            $table->string('pendidikanayah', 50)->nullable();
            $table->integer('thlahiribu')->nullable();
            $table->string('pendidikanibu', 50)->nullable();
            $table->integer('sumberbiaya')->nullable();
            $table->string('lembaga', 200)->nullable();
            $table->integer('jenis_lembaga')->nullable();
            $table->string('jenis_tempattinggal', 50)->nullable();
            $table->string('transportasi', 200)->nullable();
            $table->string('minat', 200)->nullable();
            $table->integer('infopolije')->nullable();
            $table->integer('semester')->nullable();
            $table->integer('mahasiswa_pembiayaan')->nullable();
            $table->integer('ijin_login')->nullable();
            $table->string('kode_transaksi', 11)->nullable();
            $table->integer('kirim_tagih_foto')->default(0)->nullable();
            $table->string('nomor_ijazah', 100)->nullable();
            $table->integer('tanda')->nullable();
            $table->integer('biaya_lain')->nullable();
            $table->string('nik_ktp')->nullable();
            $table->string('jalan', 255)->nullable();
            $table->string('rt', 30)->nullable();
            $table->string('rw', 30)->nullable();
            $table->string('kelurahan', 255)->nullable();
            $table->string('kecamatan', 255)->nullable();
            $table->string('kabupaten_kota', 255)->nullable();
            $table->string('propinsi', 255)->nullable();
            $table->string('kode_pos', 5)->nullable();
            $table->string('tempat_lahir_ayah', 255)->nullable();
            $table->string('tempat_lahir_ibu', 255)->nullable();
            $table->date('tanggal_lahir_ayah')->nullable();
            $table->date('tanggal_lahir_ibu')->nullable();
            $table->string('pendidikan_ayah', 255)->nullable();
            $table->string('pendidikan_ibu', 255)->nullable();
            $table->string('jalan_ortu', 255)->nullable();
            $table->string('rt_ortu', 30)->nullable();
            $table->string('rw_ortu', 30)->nullable();
            $table->string('kelurahan_ortu', 255)->nullable();
            $table->string('kecamatan_ortu', 255)->nullable();
            $table->string('kabupaten_kota_ortu', 255)->nullable();
            $table->string('propinsi_ortu', 255)->nullable();
            $table->string('kode_pos_ortu', 5)->nullable();
            $table->integer('tahun_lulus')->nullable();
            $table->integer('semester_lulus')->nullable();
            $table->integer('feeder_wilayah')->nullable();
            $table->string('nrp_lama', 20)->nullable();
            $table->date('tglterbit')->nullable();
            $table->string('blangko_ijazah', 255)->nullable();
            $table->string('email', 200)->nullable();
            $table->string('pin_pddikti', 255)->nullable();
            $table->string('akreditasi', 255)->nullable();
            $table->string('sk_akreditasi', 255)->nullable();
            

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('datamahasiswa');
    }
}
