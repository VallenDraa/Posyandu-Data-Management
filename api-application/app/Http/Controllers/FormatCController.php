<?php

namespace App\Http\Controllers;

use App\Http\Requests\FormatCRequest;
use App\Models\FormatCModel;
use App\Models\OrangTuaModel;
use App\Models\PosyanduModel;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FormatCController extends Controller
{
    public function get(FormatCRequest $request): JsonResponse
    {

        /**
         * Memeriksa apakah request sesuai
         * dengan ketentuan berlaku
         * 
         */
        $data = $request->validated();

        /**
         * Membuat query utama
         * 
         */
        $query = FormatCModel::select(
            'orang_tua.nama_ibu',
            'orang_tua.nama_ayah',
            'orang_tua.nik_ibu',
            'orang_tua.nik_ayah',
            'orang_tua.tanggal_meninggal_ibu',
            'orang_tua.no_telp',
            'orang_tua.rt_rw',
            'orang_tua.tempat_tinggal',
            'format_c.id as id_format_c',
            'format_c.umur',
            'format_c.tahapan_ks',
            'format_c.kelompok_dasawisma',
            'format_c.lila',
            'format_c.jumlah_anak_hidup',
            'format_c.jumlah_anak_meninggal',
            'format_c.imunisasi',
            'format_c.jenis_kontrasepsi',
            'format_c.tanggal_penggantian',
            'format_c.penggantian_jenis_kontrasepsi',
            'format_c.keterangan',
        )->join('orang_tua', 'orang_tua.id', 'format_c.id_orang_tua')
            ->orderByDesc('format_c.created_at');

        /**
         * Melakukan filtering atau penyaringan
         * data pada kondisi tertentu
         * 
         */
        if (!empty($data['search'])) {

            /**
             * Memfilter data sesuai request search
             * 
             */
            $query = $query->where('orang_tua.nama_ayah', 'LIKE', '%' . $data['search'] . '%')
                ->orWhere('orang_tua.nama_ibu', 'LIKE', '%' . $data['search'] . '%');

        }

        /**
         * Mengambil banyaknya data yang diambil
         * 
         */
        $count = $query->count();

        /**
         * Memeriksa apakah data ingin difilter
         * 
         */
        if (isset($data['start']) && isset($data['length'])) {
            $query = $query->offset(($data['start'] - 1) * $data['length'])
                ->limit($data['length']);
        }

        /**
         * Mendapatkan data format c
         * 
         */
        $formatC = $query->get();

        /**
         * Mengambil data posyandu
         * 
         */
        $posyandu = PosyanduModel::select(
            'nama_posyandu',
            'kota'
        )->first();

        /**
         * Mengubah data array dari imunisasi
         * 
         */
        $formatC = $formatC->map(function ($item) {
            $item->imunisasi = explode(',', $item->imunisasi);
            return $item;
        });

        /**
         * Memeberikan data yang diminta
         * melalui response
         * 
         */
        return response()->json([
            'jumlah_data' => $count,
            'format_c' => $formatC,
            'judul_format' => 'Register WUS dan PUS dalam wilayah kerja posyandu',
            'nama_posyandu' => $posyandu->nama_posyandu,
            'kota' => $posyandu->kota,
        ])->setStatusCode(200);
    }
    public function post(FormatCRequest $request): JsonResponse
    {

        /**
         * Memeriksa apakah request sesuai
         * dengan ketentuan berlaku
         * 
         */
        $data = $request->validated();

        /**
         * Memeriksa apakah data yang
         * dibutuhkan sudah tersedia
         * 
         */
        if (empty($data['nama_ibu']) && empty($data['id_orang_tua'])) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => 'Data nama ibu tidak boleh kosong',
                    'test' => $data
                ]
            ])->setStatusCode(400));
        }

        if (empty($data['id_orang_tua'])) {

            /**
             * Melakukan penambahan data orang_tua
             * 
             */
            $orangTua = OrangTuaModel::create([
                'nama_ayah' => $data['nama_ayah'],
                'nik_ayah' => $data['nik_ayah'],
                'nama_ibu' => $data['nama_ibu'],
                'nik_ibu' => $data['nik_ibu'],
                'tanggal_meninggal_ibu' => $data['tanggal_meninggal_ibu'],
                'no_telp' => $data['no_telp'],
                'rt_rw' => $data['rt_rw'],
                'tempat_tinggal' => $data['tempat_tinggal'],
            ]);

            /**
             * Inisiasi id data orang tua
             * 
             */
            $data['id_orang_tua'] = $orangTua->id;

        }

        /**
         * Menambahkan data format c
         * 
         */
        FormatCModel::create([
            'id_orang_tua' => $data['id_orang_tua'],
            'umur' => $data['umur'],
            'tahapan_ks' => $data['tahapan_ks'],
            'kelompok_dasawisma' => $data['kelompok_dasawisma'],
            'lila' => $data['lila'],
            'jumlah_anak_hidup' => $data['jumlah_anak_hidup'],
            'jumlah_anak_meninggal' => $data['jumlah_anak_meninggal'],
            'imunisasi' => empty($data['imunisasi']) ? '' : implode(',', $data['imunisasi']),
            'jenis_kontrasepsi' => $data['jenis_kontrasepsi'],
            'tanggal_penggantian' => $data['tanggal_penggantian'],
            'penggantian_jenis_kontrasepsi' => $data['penggantian_jenis_kontrasepsi'],
            'keterangan' => $data['keterangan'],
        ]);

        /**
         * Mengembalikan response setelah
         * melakukan penambahan data
         * 
         */
        return response()->json([
            'success' => [
                'message' => "Data berhasil ditambahkan"
            ]
        ])->setStatusCode(201);
    }

    public function put(FormatCRequest $request): JsonResponse
    {

        /**
         * Memeriksa apakah request sesuai
         * dengan ketentuan berlaku
         * 
         */
        $data = $request->validated();

        /**
         * Membuat query utama
         * 
         */
        $formatC = FormatCModel::select(
            'orang_tua.id as id_orang_tua',
            'orang_tua.nama_ibu',
            'orang_tua.nama_ayah',
            'orang_tua.nik_ibu',
            'orang_tua.nik_ayah',
            'orang_tua.tanggal_meninggal_ibu',
            'orang_tua.no_telp',
            'orang_tua.rt_rw',
            'orang_tua.tempat_tinggal',
            'format_c.umur',
            'format_c.tahapan_ks',
            'format_c.kelompok_dasawisma',
            'format_c.lila',
            'format_c.jumlah_anak_hidup',
            'format_c.jumlah_anak_meninggal',
            'format_c.imunisasi',
            'format_c.jenis_kontrasepsi',
            'format_c.tanggal_penggantian',
            'format_c.penggantian_jenis_kontrasepsi',
            'format_c.keterangan',
        )
            ->join('orang_tua', 'orang_tua.id', 'format_c.id_orang_tua')
            ->where('format_c.id', $data['id_format_c'])
            ->first();

        /**
         * Melakukan pengubahan data bayi
         * 
         */
        $wusPus = FormatCModel::where('id', $data['id_format_c']);
        $wusPus->update([
            'id_orang_tua' => $data['ganti_id_ortu'] ?? $formatC->id_orang_tua,
            'umur' => $data['umur'] ?? $formatC->umur,
            'tahapan_ks' => $data['tahapan_ks'] ?? $formatC->tahapan_ks,
            'kelompok_dasawisma' => $data['kelompok_dasawisma'] ?? $formatC->kelompok_dasawisma,
            'lila' => $data['lila'] ?? $formatC->lila,
            'jumlah_anak_hidup' => $data['jumlah_anak_hidup'] ?? $formatC->jumlah_anak_hidup,
            'jumlah_anak_meninggal' => $data['jumlah_anak_meninggal'] ?? $formatC->jumlah_anak_meninggal,
            'imunisasi' => $data['imunisasi'] ? implode(',', $data['imunisasi']) : $formatC->imunisasi,
            'jenis_kontrasepsi' => $data['jenis_kontrasepsi'] ?? $formatC->jenis_kontrasepsi,
            'tanggal_penggantian' => $data['tanggal_penggantian'] ?? $formatC->tanggal_penggantian,
            'penggantian_jenis_kontrasepsi' => $data['penggantian_jenis_kontrasepsi'] ?? $formatC->penggantian_jenis_kontrasepsi,
            'keterangan' => $data['keterangan'] ?? $formatC->keterangan,
        ]);

        $wusPus = $wusPus->select('id_orang_tua')->first();

        if (empty($data['ganti_id_ortu'])) {

            /**
             * Melakukan pengubahan data orang_tua
             * 
             */
            OrangTuaModel::where('id', $wusPus->id_orang_tua)->update([
                'nama_ayah' => $data['nama_ayah'] ?? $formatC->nama_ayah,
                'nama_ibu' => $data['nama_ibu'] ?? $formatC->nama_ibu,
                'nik_ayah' => $data['nik_ayah'] ?? $formatC->nik_ayah,
                'nik_ibu' => $data['nik_ibu'] ?? $formatC->nik_ibu,
                'tanggal_meninggal_ibu' => $data['tanggal_meninggal_ibu'] ?? $formatC->tanggal_meninggal_ibu,
                'no_telp' => $data['no_telp'] ?? $formatC->no_telp,
                'tempat_tinggal' => $data['tempat_tinggal'] ?? $formatC->tempat_tinggal,
                'rt_rw' => $data['rt_rw'] ?? $formatC->rt_rw,
            ]);

        }

        /**
         * Mengembalikan response setelah
         * melakukan pengubahan data
         * 
         */
        return response()->json([
            'success' => [
                'message' => "Data berhasil diubah"
            ]
        ])->setStatusCode(201);
    }
    public function delete(FormatCRequest $request): JsonResponse
    {
        $data = $request->validated();

        /**
         * Mendapatkan id_orang tua
         * 
         */
        FormatCModel::where('format_c.id', $data['id_format_c'])
            ->delete();

        /**
         * Mengembalikan response setelah
         * melakukan penambahan data
         * 
         */
        return response()->json([
            'success' => [
                'message' => "Data berhasil ditambahkan"
            ]
        ])->setStatusCode(201);
    }
}
