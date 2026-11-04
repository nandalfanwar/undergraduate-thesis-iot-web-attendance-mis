<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\DataKehadiran;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RFIDController extends Controller
{
    private const JAM_MASUK = '07:00:00';
    private const JAM_PULANG = '12:00:00';
    private const MIN_CLOCK_OUT_MINUTES = 60; //gap tap in and tap out. might vary based on your needs. I set it to 60 minutes for this example
    private const STATUS_HADIR = 1;
    private const STATUS_TERLAMBAT = 7;

    public function store(Request $request)
    {
        $uid = strtolower(trim($request->uid));

        /*
        |--------------------------------------------------------------------------
        | MODE PENDAFTARAN RFID
        | mode is activated after the user (staf kedisiplinan) clicks the "Daftarkan RFID" button in the user management page. 
        | The button will create a file called rfid_target.txt in the storage folder, which contains the user ID 
        | of the user that is being registered. The RFID reader will then send the UID to this endpoint, and if 
        | the file shows the following user ID exists, it will 
        | and if it is not, then it will register the UID to the user ID in the file.
        |--------------------------------------------------------------------------
        */

        if (Storage::exists('rfid_target.txt')) {

            $userId = trim(Storage::get('rfid_target.txt'));

            $user = User::find($userId);

            if (!$user) {
                Storage::delete('rfid_target.txt');
                return response('USER TIDAK DITEMUKAN', 404);
            }

            $user->update([
                'uid_rfid' => $uid
            ]);

            Storage::delete('rfid_target.txt');

            return response('RFID BERHASIL DIDAFTARKAN');
        }


        /*
        |--------------------------------------------------------------------------
        | MODE PRESENSI
        | This is the normal mode of operation. The RFID reader will send the UID to this endpoint, and if the UID is registered to a user, it will record the attendance for that user.
        | If the UID is not registered to a user, it will return an error message.
        |--------------------------------------------------------------------------
        */

        $user = User::where('uid_rfid', $uid)->first();

        if (!$user) {
            return response('UID TIDAK TERDAFTAR', 404);
        }

        $today = Carbon::today();

        $kehadiran = DataKehadiran::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->first();

        /*
        |--------------------------------------------------------------------------
        | CLOCK IN
        | If the user has not clocked in yet, it will record the clock in time and status (on time or late) based on the current time.
        | If the user has already clocked in, it will check if the user has clocked out. If not, it will check if the user can clock out based on the minimum clock
        |--------------------------------------------------------------------------
        */

        if (!$kehadiran) {

            // DataKehadiran::create([

            //     'user_id' => $user->id,

            //     'tanggal' => $today,

            //     'waktu_masuk' => Carbon::now(),

            //     'waktu_keluar' => null,

            //     'status_kehadiran_id' => 1,

            //     'created_at' => now(),

            //     'updated_at' => now()

            // ]);

            $sekarang = Carbon::now();

            $status = $sekarang->format('H:i:s') <= self::JAM_MASUK
                ? self::STATUS_HADIR
                : self::STATUS_TERLAMBAT;

            DataKehadiran::create([

                'user_id' => $user->id,

                'tanggal' => $today,

                'waktu_masuk' => $sekarang,

                'waktu_keluar' => null,

                'status_kehadiran_id' => $status,

                'keterangan' => null,

            ]);

            return response('CLOCK IN BERHASIL');
        }

        /*
        |--------------------------------------------------------------------------
        | CLOCK OUT
        | If the user has already clocked in, it will check if the user has clocked out. If not, it will check if the user can clock out based on the minimum clock out minutes.
        | If the user can clock out, it will record the clock out time and any relevant keterangan (e.g. if the user is clocking out before the end of the work day with "Pulang sebelum jam kerja.").
        | If the user has already clocked out, it will return a message indicating that the user has already clocked out for the day with "ANDA SUDAH MELAKUKAN PRESENSI HARI INI".
        |--------------------------------------------------------------------------
        */

        if (is_null($kehadiran->waktu_keluar)) {

            // $kehadiran->update([

            //     'waktu_keluar' => Carbon::now()

            // ]);

            $jamMasuk = Carbon::parse($kehadiran->waktu_masuk);

            if ($jamMasuk->diffInMinutes(Carbon::now()) < self::MIN_CLOCK_OUT_MINUTES) {
                return response('CLOCK OUT BELUM DAPAT DILAKUKAN');
            }

            $sekarang = Carbon::now();

            $keterangan = null;

            if ($sekarang->format('H:i:s') < self::JAM_PULANG) {
                $keterangan = 'Pulang sebelum jam kerja.';
            }

            $kehadiran->update([

                'waktu_keluar' => $sekarang,
                'keterangan' => $keterangan

            ]);

            return response('CLOCK OUT BERHASIL');
        }

        /*
        |--------------------------------------------------------------------------
        | SUDAH PRESENSI HARI INI
        |--------------------------------------------------------------------------
        */

        return response('ANDA SUDAH MELAKUKAN PRESENSI HARI INI');
    }

    // public function store(Request $request)
    // {
    //     \Log::info('RFID REQUEST MASUK', [
    //         'uid' => $request->uid,
    //         'time' => now(),
    //     ]);

    //     return response('TEST');
    // }

//     public function store(Request $request)
//     {
//         return response('OK');
//     }
}