<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\UserModel;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\ImageManagerStatic as Image;

class AuthUserController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        /**
         * Melakukan pengecekan data apakah
         * data merupakan data yang valid
         *
         */
        $data = $request->validate([
            "email" => "required|string",
            "password" => "required|string",
        ]);

        /**
         * Mengambil data programmer dari tabel
         * berdasarkan email yang diberikan
         *
         */
        $user = UserModel::where('email', $data['email'])
            ->first();

        /**
         * Memeriksa apakah data admin ditemukan
         *
         */
        if (empty($user)) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => 'Email atau password salah'
                ]
            ])->setStatusCode(401));
        }

        /**
         * Memeriksa apakah password yang
         * diberikan benar dan sesuai
         *
         */
        $passwordIsCorrect = Hash::check($request->password, $user->password);

        /**
         * Kembalikan unauthorize response jika
         * ada kesalahan pada email dan
         * password yang diberikan maka
         *
         */
        if (!$passwordIsCorrect) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => 'Email atau password salah'
                ]
            ])->setStatusCode(401));
        }

        /**
         * Membuat token dan mendapatkan
         * token berupa string
         *
         */
        $token = $user->createToken('personal_access_tokens', ['server:update'], null)->plainTextToken;

        /**
         * Kembalikan response yang sesuai
         *
         */
        return response()->json([
            'success' => [
                'message' => 'Berhasil login'
            ],
            'nama' => $user->nama,
            'email' => $user->email,
            'whatsapp' => $user->whatsapp,
            'poin' => $user->poin,
            'token' => $token
        ])->setStatusCode(200);
    }

    public function logout(Request $request): JsonResponse
    {
        /**
         * Hapus token dari database
         *
         */
        Auth::user()->currentAccessToken()->delete();

        /**
         * Kembalikan response yang sesuai
         *
         */
        return response()->json([
            'success' => [
                'message' => 'Berhasil logout'
            ]
        ])->setStatusCode(200);
    }

    public function authData()
    {
        /**
         * Hapus token dari database
         *
         */
        $user = UserModel::select([
            'nama',
            'email',
            'whatsapp',
            'poin',
            'foto_profile',
        ])->findOrFail(Auth::user()->id);

        /**
         * Kembalikan response yang sesuai
         *
         */
        return response()->json($user)->setStatusCode(200);
    }
    public function resetPassword(Request $request): JsonResponse
    {
        /**
         * Melakukan pengecekan data apakah
         * data merupakan data yang valid
         *
         */
        $data = $request->validate([
            "old_password" => "required|string",
            "new_password" => "required|string",
            "confirm_password" => "required|string",
        ]);

        /**
         * Mendapatkan data user
         *
         */
        $user = UserModel::findOrFail(Auth::user()->id);

        /**
         * Memeriksa apakah password yang
         * diberikan benar dan sesuai
         *
         */
        $passwordIsCorrect = Hash::check($data["old_password"], $user->password);

        /**
         * Kembalikan unauthorize response jika
         * ada kesalahan pada email dan
         * password yang diberikan maka
         *
         */
        if (!$passwordIsCorrect) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => 'Email atau password salah'
                ]
            ])->setStatusCode(401));
        }

        /**
         * Memeriksa apakah kedua password
         * baru adalah password yang sama
         *
         */
        if ($data['new_password'] != $data['confirm_password']) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => 'Password baru harus sama!'
                ]
            ])->setStatusCode(400));
        }

        /**
         * Memeriksa apakah kedua password
         * baru adalah password yang sama
         *
         */
        if ($data['new_password'] == $data['old_password']) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => 'Tidak boleh menggunakan password sebelumnya!'
                ]
            ])->setStatusCode(400));
        }

        /**
         * Memeriksa apakah kedua password
         * baru adalah password yang sama
         *
         */
        if (strlen($data['new_password']) < 6) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => 'Password minimal harus 6 karakter!'
                ]
            ])->setStatusCode(400));
        }

        /**
         * Enkripsi data password
         *
         */
        $user->update([
            'password' => Hash::make($data['new_password'])
        ]);

        /**
         * Mengembalikan response yang sesuai
         *
         */
        return response()->json([
            'success' => [
                'message' => 'Password berhasil diubah'
            ]
        ])->setStatusCode(200);
    }

    public function register(UserRequest $request): JsonResponse
    {
        $data = $request->validated();

        $data['password'] = Hash::make($data['password']);

        UserModel::create($data);

        return response()->json([
            "success" => [
                "message" => "Register berhasil"
            ]
        ])->setStatusCode(201);
    }
    public function put(UserRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = UserModel::findOrFail(Auth::user()->id);

        if (!empty($data['foto_profile']) && $data['foto_profile'] != $user->foto_profile) {
            /**
             * 'upload' adalah subfolder tempat gambar akan disimpan
             * di sistem penyimpanan yang Anda konfigurasi
             */
            $base64Parts = explode(",", $data['foto_profile']);
            $base64Image = end($base64Parts);

            $decodedImage = base64_decode($base64Image);

            /**
             * Membuat instance Intervention Image
             *
             */
            $img = Image::make($decodedImage);

            /**
             * Tentukan ekstensi yang diinginkan
             * (jpg, jpeg, atau png)
             *
             */
            $extension = 'jpg';

            /**
             * Mengidentifikasi tipe MIME gambar
             *
             */
            $mime = finfo_buffer(finfo_open(), $decodedImage, FILEINFO_MIME_TYPE);

            /**
             * Jika tipe MIME adalah gambar JPEG,
             * maka set ekstensi menjadi 'jpg'
             *
             */
            if ($mime === 'image/jpeg') {
                $extension = 'jpeg';
            }

            /**
             * Jika tipe MIME adalah gambar PNG,
             * maka set ekstensi menjadi 'png'
             *
             */
            if ($mime === 'image/png') {
                $extension = 'png';
            }

            $namaFile = Auth::user()->id . Carbon::now()->format('Y-m-d') . '_' . time() . '.' . $extension;

            /**
             * Simpan gambar ke folder
             *
             */
            $path = 'images/upload/' . $namaFile;
            $img->save(public_path($path), 80);
            $data['foto_profile'] = '/' . $path;
        }

        $user->update($data);

        return response()->json([
            "success" => [
                "message" => "Data user berhasil diperbarui"
            ]
        ])->setStatusCode(200);
    }
}
