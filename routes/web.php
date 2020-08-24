<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});


$router->get('key', 'AuthController@generateKey');
$router->post('ujicoba', 'UjicobaController@ujicoba');

// -- Router Mahasiswa -- //
$router->post('loginmhs', 'AuthUserController@loginMahasiswa');
$router->get('jadwalmhs', 'JadwalController@jadwalMhs');
$router->post('absensi', 'AbsenController@absensi');
$router->post('izin', 'IzinController@izin');
$router->post('suratizin', 'IzinController@SuratIzin');
$router->post('deleteizin', 'IzinController@delete');
$router->get('pilihmatkul', 'PilihmatkulController@pilihmatkul');
$router->get('histori', 'HistoriController@histori');
$router->get('filtermatkul', 'FiltermatkulController@filtermatkul');
$router->post('mhstoken', 'StoreTokenMhsController@index');
$router->get('getmhstoken', 'StoreTokenMhsController@MhsToken');

// -- Router Dosen -- //
$router->post('logindsn', 'AuthUserController@loginDosen');
$router->get('jadwaldsn', 'JadwalController@jadwalDsn');
$router->get('matkulubahjdwl', 'UbahjadwalController@matakuliah');
$router->put('ubahjdwlpertemuan', 'UbahjadwalController@ubahjadwalpertemuan');
$router->get('historidsn', 'HistoridosenController@historidosen');
$router->put('ubahkehadiran', 'HistoridosenController@ubahkehadiran_mhs');
$router->get('historimatkul', 'HistoridosenController@matkul');
$router->get('historikelas', 'HistoridosenController@kelas');
$router->get('historiminggu', 'HistoridosenController@minggu');
$router->get('izindosen', 'IzindosenController@izindosen');
$router->post('deleteizin', 'IzindosenController@deleteizin');
$router->put('changestatus', 'PdfViewController@changestatus');
$router->post('dsntoken', 'StoreTokenDsnController@index');