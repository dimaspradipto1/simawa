<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PrestasiMandiriRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'level'                   => ['required', 'string'],
            'kategori'                => ['required', 'string'],
            'nama_kompetisi'          => ['required', 'string', 'max:255'],
            'nama_cabang'             => ['required', 'string', 'max:255'],
            'peringkat'               => ['required', 'string'],
            'nama_penyelenggara'      => ['required', 'string', 'max:255'],
            'jumlah_pt_peserta'       => ['required', 'integer', 'min:1'],
            'kepesertaan'             => ['required', 'string'],
            'bentuk'                  => ['required', 'string'],
            'url_kompetisi'           => ['required', 'url', 'max:255'],
            'link_dokumen_sertifikat' => ['nullable', 'string', 'max:255'],
            'file_dokumen_sertifikat' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:20480'],
            'tanggal_sertifikat'      => ['required', 'date'],
            'link_foto_upp'           => ['nullable', 'string', 'max:255'],
            'file_foto_upp'           => ['nullable', 'file', 'mimes:png,jpg,jpeg', 'max:10240'],
            'link_dokumen_undangan'   => ['nullable', 'url', 'max:255'],
            'link_dokumen_lpj'        => ['nullable', 'url', 'max:255'],
            'file_dokumen_lpj'        => ['nullable', 'file', 'mimes:pdf,doc,docx,zip', 'max:20480'],
            'no_hp'                   => ['required', 'string', 'max:30'],
            'file_pengajuan_reward'   => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:20480'],
            'link_pengajuan_reward'   => ['nullable', 'string', 'max:255'],
            'file_ktm'                => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'link_ktm'                => ['nullable', 'string', 'max:255'],
            'file_surat_tugas'        => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:20480'],
            'link_surat_tugas'        => ['nullable', 'string', 'max:255'],
            'keterangan'              => ['nullable', 'string'],
            'data_mahasiswa'          => ['nullable', 'array'],
            'data_mahasiswa.*.nim'    => ['nullable', 'string'],
            'data_mahasiswa.*.nama'   => ['nullable', 'string'],
            'data_dosen'              => ['nullable', 'array'],
            'data_dosen.*.nidn'       => ['nullable', 'string'],
            'data_dosen.*.nama'       => ['nullable', 'string'],
            'data_dosen.*.url_surat'  => ['nullable', 'string'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'level.required'                => 'Level wajib dipilih.',
            'kategori.required'             => 'Kategori wajib dipilih.',
            'nama_kompetisi.required'       => 'Nama Kompetisi / Lomba wajib diisi.',
            'nama_cabang.required'          => 'Nama Cabang wajib diisi.',
            'peringkat.required'            => 'Peringkat wajib dipilih.',
            'nama_penyelenggara.required'   => 'Nama Penyelenggara wajib diisi.',
            'jumlah_pt_peserta.required'    => 'Jumlah PT Peserta wajib diisi.',
            'jumlah_pt_peserta.integer'     => 'Jumlah PT Peserta harus berupa angka.',
            'jumlah_pt_peserta.min'         => 'Jumlah PT Peserta minimal 1.',
            'kepesertaan.required'          => 'Kepesertaan wajib dipilih.',
            'bentuk.required'               => 'Bentuk kegiatan wajib dipilih.',
            'url_kompetisi.required'        => 'URL Kompetisi / Lomba wajib diisi.',
            'url_kompetisi.url'             => 'Format URL tidak valid (gunakan http:// atau https://).',
            'tanggal_sertifikat.required'   => 'Tanggal Sertifikat wajib diisi.',
            'tanggal_sertifikat.date'       => 'Format tanggal sertifikat tidak valid.',
            'no_hp.required'                => 'No. HP / WhatsApp wajib diisi.',
            'file_dokumen_sertifikat.mimes' => 'Format file sertifikat harus .pdf, .jpg, .jpeg, atau .png.',
            'file_dokumen_sertifikat.max'   => 'Ukuran file sertifikat maksimal 20 MB.',
            'file_foto_upp.mimes'           => 'Format foto harus .png, .jpg, atau .jpeg.',
            'file_foto_upp.max'             => 'Ukuran foto maksimal 10 MB.',
            'file_dokumen_lpj.mimes'        => 'Format file LPJ harus .pdf, .doc, .docx, atau .zip.',
            'file_dokumen_lpj.max'          => 'Ukuran file LPJ maksimal 20 MB.',
            'file_ktm.mimes'                => 'Format file KTM harus .pdf, .jpg, .jpeg, atau .png.',
            'file_ktm.max'                  => 'Ukuran file KTM maksimal 10 MB.',
            'file_pengajuan_reward.mimes'   => 'Format berkas reward harus .pdf, .doc, atau .docx.',
            'file_pengajuan_reward.max'     => 'Ukuran berkas reward maksimal 20 MB.',
            'file_surat_tugas.mimes'        => 'Format surat tugas harus .pdf, .doc, atau .docx.',
            'file_surat_tugas.max'          => 'Ukuran surat tugas maksimal 20 MB.',
        ];
    }
}
