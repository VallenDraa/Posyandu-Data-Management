<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProdukRequest;
use App\Models\ProdukModel;
use App\Models\ProdukTagModel;
use App\Models\TagModel;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\Auth;

class ProdukController extends Controller
{
    public function get(ProdukRequest $request): JsonResponse
    {
        $data = $request->validated();

        $query = ProdukModel::select(
            "id",
            "nomor_telepon",
            "nama",
            "deskripsi",
            "overview",
            "harga",
            "gambar",
            "produk.created_at"
        )->where("sedang_dijual", true);

        if (!empty($data["tags"])) {
            $tags = $data["tags"];
            foreach ($tags as $tag) {
                $query->whereHas('tags', function ($query) use ($tag) {
                    $query->where('tag', $tag);
                });
            }
        }

        if (!empty($data["search"])) {
            $query->where(function ($query) use ($data) {
                $query->where("deskripsi", "like", "%{$data["search"]}%")
                    ->orWhere("nama", "like", "%{$data["search"]}%")
                    ->orWhere("overview", "like", "%{$data["search"]}%");
            });
        }

        if (!empty($data["sort"])) {
            switch ($data["sort"]) {
                case 'terbaru':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'terlama':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'a-z':
                    $query->orderBy('nama', 'asc');
                    break;
                case 'z-a':
                    $query->orderBy('nama', 'desc');
                    break;
                case 'termahal':
                    $query->orderBy('harga', 'desc');
                    break;
                case 'termurah':
                    $query->orderBy('harga', 'asc');
                    break;
            }
        }

        $produk = $query->paginate($data["length"]);

        $tmp_data = $produk->getCollection()->map(function ($item) {

            $item["tags"] = TagModel::select("tag")->join("produk_tag", "produk_tag.tag_id", "tag.id")
                ->where('produk_tag.produk_id', $item->id)
                ->pluck('tag')
                ->toArray();

            return $item;

        });

        $produk = $produk->setCollection($tmp_data);

        return response()->json($produk)->setStatusCode(200);
    }
    public function post(ProdukRequest $request): JsonResponse
    {
        $data = $request->validated();

        if (!empty($data['gambar'])) {
            /**
             * 'upload' adalah subfolder tempat gambar akan disimpan
             * di sistem penyimpanan yang Anda konfigurasi
             */
            $base64Parts = explode(",", $data['gambar']);
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
            $data['gambar'] = '/' . $path;
        }

        if (isset($data['pin']) && $data['pin']) {
            $pinCount = ProdukModel::where('pin', true)->count();
            if ($pinCount > 4) {
                return response()->json([
                    "errors" => [
                        "message" => "Jumlah produk yang dipin tidak boleh lebih dari 5"
                    ]
                ])->setStatusCode(422);
            }
        }

        $produk = ProdukModel::create([
            "admin_id" => Auth::user()->id,
            "nomor_telepon" => $data["nomor_telepon"],
            "nama" => $data["nama"],
            "deskripsi" => $data["deskripsi"],
            "overview" => $data["overview"],
            "harga" => $data["harga"],
            "gambar" => $data["gambar"],
            "sedang_dijual" => $data["sedang_dijual"],
            "pin" => $data["pin"],
        ]);

        foreach ($data["tags"] as $item) {
            $tag = TagModel::firstOrCreate(["tag" => strtolower($item)]);

            ProdukTagModel::create([
                "produk_id" => $produk->id,
                "tag_id" => $tag->id,
            ]);
        }

        return response()->json([
            "success" => [
                "message" => "Produk berhasil ditambahkan!"
            ]
        ])->setStatusCode(201);
    }
    public function put(ProdukRequest $request, $id): JsonResponse
    {
        $data = $request->validated();

        $produk = ProdukModel::findOrFail($id);

        if (!empty($data['gambar']) && $data['gambar'] != $produk->gambar) {
            /**
             * 'upload' adalah subfolder tempat gambar akan disimpan
             * di sistem penyimpanan yang Anda konfigurasi
             */
            $base64Parts = explode(",", $data['gambar']);
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
            $data['gambar'] = '/' . $path;
        }

        if (isset($data['pin']) && $data['pin'] && !$produk->pin) {
            $pinCount = ProdukModel::where('pin', true)->count();
            if ($pinCount > 4) {
                return response()->json([
                    "errors" => [
                        "message" => "Jumlah produk yang dipin tidak boleh lebih dari 5"
                    ]
                ])->setStatusCode(422);
            }
        }

        $produk->update([
            "nomor_telepon" => $data["nomor_telepon"],
            "nama" => $data["nama"],
            "deskripsi" => $data["deskripsi"],
            "overview" => $data["overview"],
            "harga" => $data["harga"],
            "gambar" => $data["gambar"],
            "sedang_dijual" => $data["sedang_dijual"],
            "pin" => $data["pin"],
        ]);

        $currentTags = $produk->tags()->pluck('tag')->toArray();
        $newTags = array_map('strtolower', $data['tags']);

        foreach (array_diff($currentTags, $newTags) as $tagToRemove) {
            $tag = TagModel::where('tag', $tagToRemove)->first();
            $produk->tags()->detach($tag->id);
        }

        foreach (array_diff($newTags, $currentTags) as $item) {
            $tag = TagModel::firstOrCreate(["tag" => $item]);
            $produk->tags()->attach($tag->id);
        }

        return response()->json([
            "success" => [
                "message" => "Produk berhasil diperbarui"
            ]
        ])->setStatusCode(200);
    }
    public function delete($id): JsonResponse
    {
        ProdukModel::findOrFail($id)
            ->delete();

        return response()->json([
            "success" => [
                "message" => "Produk berhasil dihapus dari peredaran"
            ]
        ])->setStatusCode(200);
    }
    public function pin(): JsonResponse
    {
        return response()->json(
            ProdukModel::select(
                "id",
                "nomor_telepon",
                "nama",
                "deskripsi",
                "overview",
                "harga",
                "gambar",
                "produk.created_at",
            )->where("pin", true)
                ->where("sedang_dijual", true)
                ->get()
                ->map(function ($item) {
                    $item["tags"] = TagModel::select("tag")->join("produk_tag", "produk_tag.tag_id", "tag.id")
                        ->where('produk_tag.produk_id', $item->id)
                        ->pluck('tag')
                        ->toArray();
                    return $item;
                })
        )->setStatusCode(200);
    }
    public function tags(): JsonResponse
    {
        return response()->json(
            TagModel::pluck('tag')
                ->toArray()
        )->setStatusCode(200);
    }

    public function getSpesific($id): JsonResponse
    {
        return response()->json([
            ...ProdukModel::select(
                "nomor_telepon",
                "nama",
                "deskripsi",
                "overview",
                "harga",
                "gambar",
                "sedang_dijual",
                "pin",
                "produk.created_at",
            )->findOrFail($id)->toArray(),
            "tags" => TagModel::select("tag")->join("produk_tag", "produk_tag.tag_id", "tag.id")
                ->where('produk_tag.produk_id', $id)
                ->pluck('tag')
                ->toArray()
        ])->setStatusCode(200);
    }
}
