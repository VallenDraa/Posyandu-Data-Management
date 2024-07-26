<?php

namespace App\Http\Requests;

class PosyanduRequest extends CoreRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        switch ($this->getMethod()) {
            case 'PUT':
                return [
                    'nama_posyandu' => 'nullable|string',
                    'kota' => 'nullable|string',
                    'kecamatan' => 'nullable|string',
                    'kelurahan' => 'nullable|string',
                    'rt_rw' => 'nullable|string',
                    'penyampaian_ketua' => 'nullable|string',
                    'gambar_gedung' => 'nullable',
                    'gambar_struktur_organisasi' => 'nullable',
                    'visi' => 'nullable|string',
                    'misi' => 'nullable|string',
                ];
            default:
                return [];
        }
    }
}
