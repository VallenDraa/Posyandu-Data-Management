<template>
  <VRow>
    <VCol cols="12">
      <VCard title="Data orang tua">
        <VCardItem>
          <VRow class="mb-5">
            <VCol cols="12" md="4" lg="4">
              <VRadioGroup v-model="data_tersedia">
                <VRadio label="Pilih data tersedia" :value="true"></VRadio>
              </VRadioGroup>
            </VCol>
            <VCol cols="12" md="4" lg="4">
              <VRadioGroup v-model="data_tersedia">
                <VRadio label="Tambah data" :value="false"></VRadio>
              </VRadioGroup>
            </VCol>
          </VRow>
          <VRow v-if="data_tersedia">
            <VCol cols="12">
              <VAutocomplete v-model="pilihOrangTua" label="Nama WUS & PUS...." :items="listOrangTua"></VAutocomplete>
            </VCol>
          </VRow>
          <VRow v-else>
            <VCol cols="12">
              <VRow no-gutters>
                <!-- 👉 First Name -->
                <VCol cols="12" md="3">
                  <label for="ayah">Nama Ayah</label>
                </VCol>

                <VCol cols="12" md="9">
                  <VTextField id="ayah" v-model="nama_ayah" placeholder="Masukkan Nama Ayah..."
                    persistent-placeholder />
                  <sup class="text-error">*Wajib diisi</sup>
                </VCol>
              </VRow>
            </VCol>
            <VCol cols="12">
              <VRow no-gutters>
                <!-- 👉 First Name -->
                <VCol cols="12" md="3">
                  <label for="ibu">Nama Ibu</label>
                </VCol>

                <VCol cols="12" md="9">
                  <VTextField id="ibu" v-model="nama_ibu" placeholder="Masukkan Nama Ibu..." persistent-placeholder />
                  <sup class="text-error">*Wajib diisi</sup>
                </VCol>
              </VRow>
            </VCol>
            <VCol cols="12">
              <VRow no-gutters>
                <!-- 👉 First Name -->
                <VCol cols="12" md="3">
                  <label for="nik_ibu">NIK Ayah</label>
                </VCol>

                <VCol cols="12" md="9">
                  <VTextField id="nik_ayah" v-model="nik_ayah" placeholder="Masukkan NIK Ayah..."
                    persistent-placeholder />
                </VCol>
              </VRow>
            </VCol>
            <VCol cols="12">
              <VRow no-gutters>
                <!-- 👉 First Name -->
                <VCol cols="12" md="3">
                  <label for="nik_ibu">NIK Ibu</label>
                </VCol>

                <VCol cols="12" md="9">
                  <VTextField id="nik_ibu" v-model="nik_ibu" placeholder="Masukkan NIK Ibu..." persistent-placeholder />
                </VCol>
              </VRow>
            </VCol>
            <VCol cols="12">
              <VRow no-gutters>
                <!-- 👉 First Name -->
                <VCol cols="12" md="3">
                  <label for="tanggal-meninggal-ibu">Tanggal Meninggal Ibu</label>
                </VCol>

                <VCol cols="12" md="9">
                  <VTextField v-model="tanggal_meninggal_ibu" placeholder="" type="date" />
                </VCol>
              </VRow>
            </VCol>
            <VCol cols="12">
              <VRow no-gutters>
                <!-- 👉 First Name -->
                <VCol cols="12" md="3">
                  <label>Nomor yang bisa dihubungi</label>
                </VCol>

                <VCol cols="12" md="9">
                  <VTextField v-model="no_telp" placeholder="08xx" persistent-placeholder />
                </VCol>
              </VRow>
            </VCol>
            <VCol cols="12">
              <VRow no-gutters>
                <!-- 👉 First Name -->
                <VCol cols="12" md="3">
                  <label>RT/RW</label>
                </VCol>

                <VCol cols="12" md="9">
                  <VTextField v-model="rt_rw" placeholder="Masukkan RT/RW..." persistent-placeholder />
                </VCol>
              </VRow>
            </VCol>
            <VCol cols="12">
              <VRow no-gutters>
                <!-- 👉 First Name -->
                <VCol cols="12" md="3">
                  <label for="ibu">Provinsi</label>
                </VCol>

                <VCol cols="12" md="9">
                  <VAutocomplete v-model="provinceId" placeholder="Pilih...." :items="provinces" />
                </VCol>
              </VRow>
            </VCol>
            <VCol v-if="regencies.length > 0" cols="12">
              <VRow no-gutters>
                <!-- 👉 First Name -->
                <VCol cols="12" md="3">
                  <label for="ibu">Kab/Kota</label>
                </VCol>

                <VCol cols="12" md="9">
                  <VAutocomplete v-model="regencyId" placeholder="Pilih...." :items="regencies" />
                </VCol>
              </VRow>
            </VCol>
            <VCol v-if="districts.length > 0" cols="12">
              <VRow no-gutters>
                <!-- 👉 First Name -->
                <VCol cols="12" md="3">
                  <label for="ibu">Kecamatan</label>
                </VCol>

                <VCol cols="12" md="9">
                  <VAutocomplete v-model="districtId" placeholder="Pilih...." :items="districts" />
                </VCol>
              </VRow>
            </VCol>
            <VCol v-if="villages.length > 0" cols="12">
              <VRow no-gutters>
                <!-- 👉 First Name -->
                <VCol cols="12" md="3">
                  <label for="ibu">Kelurahan</label>
                </VCol>

                <VCol cols="12" md="9">
                  <VAutocomplete v-model="villageId" placeholder="Pilih...." :items="villages" />
                </VCol>
              </VRow>
            </VCol>
          </VRow>
        </VCardItem>
      </VCard>
    </VCol>
    <VCol cols="12" md="12">
      <!-- 👉 Horizontal Form -->
      <VCard title="Tambah Data Wus & Pus">
        <VCardText>
          <VRow>
            <VCol cols="12">
              <VRow no-gutters>
                <!-- 👉 First Name -->
                <VCol cols="12" md="3">
                  <label for="umur">Umur WUS dan PUS</label>
                </VCol>

                <VCol cols="12" md="9">
                  <VTextField id="umur" v-model="umur" placeholder="tahun.." persistent-placeholder type="number" />
                  <sup class="text-error">*Wajib diisi</sup>
                </VCol>
              </VRow>
            </VCol>
            <VCol cols="12">
              <VRow no-gutters>
                <!-- 👉 First Name -->
                <VCol cols="12" md="3">
                  <label for="ks">Tahapan KS</label>
                </VCol>

                <VCol cols="12" md="9">
                  <VTextField id="ks" v-model="tahapan_ks" placeholder="Tahapan KS.." persistent-placeholder />
                </VCol>
              </VRow>
            </VCol>
            <VCol cols="12">
              <VRow no-gutters>
                <!-- 👉 First Name -->
                <VCol cols="12" md="3">
                  <label for="dasawisma">Kelompok Dasawisma</label>
                </VCol>

                <VCol cols="12" md="9">
                  <VTextField id="dasawisma" v-model="kelompok_dasawisma" placeholder="Kelompok dasawisma.."
                    persistent-placeholder />
                </VCol>
              </VRow>
            </VCol>
            <VCol cols="12">
              <VRow no-gutters>
                <!-- 👉 First Name -->
                <VCol cols="12" md="3">
                  <label for="lila">Ukuran LILA</label>
                </VCol>

                <VCol cols="12" md="9">
                  <VTextField id="lila" v-model="lila" placeholder="cm.." persistent-placeholder type="number" />
                </VCol>
              </VRow>
            </VCol>
            <VCol cols="12">
              <VRow no-gutters>
                <!-- 👉 First Name -->
                <VCol cols="12" md="3">
                  <label for="tanggal-lahir">Jumlah anak</label>
                </VCol>

                <VCol cols="6" md="4">
                  <VTextField v-model="jumlah_anak_hidup" placeholder="Hidup.." />
                  <sup class="text-error">*Wajib diisi</sup>
                </VCol>

                <VCol cols="6" class="ml-5" md="4">
                  <VTextField v-model="jumlah_anak_meninggal" placeholder="Meninggal.." />
                  <sup class="text-error">*Wajib diisi</sup>
                </VCol>
              </VRow>
            </VCol>
            <VCol cols="12">
              <VRow no-gutters>
                <!-- 👉 First Name -->
                <VCol cols="12" md="3">
                  <label for="imunisasi">Imunisasi</label>
                </VCol>

                <VCol cols="12" md="9">
                  <VSelect v-model="pilihImunisasi" :items="itemImunisasi" label="imunisasi"
                    placeholder="Pilih beberapa" multiple chips>
                    <template #prepend-item>
                      <VListItem ripple @mousedown.prevent @click="toggleHasil">
                        <VListItemAction>
                          <VIcon :color="pilihImunisasi.length === itemImunisasi.length
                            ? 'primary'
                            : 'secondary'
                            ">
                            {{ iconHasil }}
                          </VIcon>
                          <Font class="ml-2"> Pilih Semua </Font>
                        </VListItemAction>
                      </VListItem>
                      <VDivider class="mt-2"></VDivider>
                    </template>
                  </VSelect>
                </VCol>
              </VRow>
            </VCol>
            <VCol cols="12">
              <VRow no-gutters>
                <!-- 👉 First Name -->
                <VCol cols="12" md="3">
                  <label for="kontrasepsi">Jenis Kontrasepsi</label>
                </VCol>

                <VCol cols="12" md="9">
                  <VTextField id="kontrasepsi" v-model="jenis_kontrasepsi" placeholder="jenis.."
                    persistent-placeholder />
                </VCol>
              </VRow>
            </VCol>
            <VCol cols="12">
              <VRow no-gutters>
                <!-- 👉 First Name -->
                <VCol cols="12" md="3">
                  <label for="penggantian">Tanggal Penggantian</label>
                </VCol>

                <VCol cols="12" md="9">
                  <VTextField id="penggantian" v-model="tanggal_penggantian" placeholder="Tanggal Penggantian.."
                    persistent-placeholder type="date" />
                </VCol>
              </VRow>
            </VCol>
            <VCol cols="12">
              <VRow no-gutters>
                <!-- 👉 First Name -->
                <VCol cols="12" md="3">
                  <label for="kontrasepsi">Jenis Pengganti</label>
                </VCol>

                <VCol cols="12" md="9">
                  <VTextField id="kontrasepsi" v-model="penggantian_jenis_kontrasepsi" placeholder="jenis kontrasepsi.."
                    persistent-placeholder />
                </VCol>
              </VRow>
            </VCol>
            <VCol cols="12">
              <VRow no-gutters>
                <!-- 👉 First Name -->
                <VCol cols="12" md="3">
                  <label for="keterangan">Keterangan</label>
                </VCol>

                <VCol cols="12" md="9">
                  <VTextField id="keterangan" v-model="keterangan" placeholder="berikan keterangan.."
                    persistent-placeholder />
                </VCol>
              </VRow>
            </VCol>
            <!-- 👉 submit and reset button -->
            <VCol offset-md="3" cols="12" md="9" class="d-flex gap-4">
              <VBtn type="submit" :disabled="isLoading" @click="submitData">
                <VProgressCircular v-if="isLoading" indeterminate color="white">
                </VProgressCircular>

                <Font v-else>Simpan</Font>
              </VBtn>
            </VCol>
          </VRow>
        </VCardText>
      </VCard>
    </VCol>
  </VRow>
</template>

<script>
import axios from "axios";
import config from "@/@core/config";
import Swal from "sweetalert2";
import { ref } from "vue";

export default {
  data() {
    return {
      isLoading: false,
      listOrangTua: [],
      data_tersedia: true,
      pilihOrangTua: null,
      nama_ayah: "",
      nik_ayah: "",
      nama_ibu: "",
      nik_ibu: "",
      tanggal_meninggal_ibu: null,
      no_telp: "",
      rt_rw: "",
      umur: null,
      tahapan_ks: "",
      kelompok_dasawisma: "",
      lila: "",
      jumlah_anak_hidup: null,
      jumlah_anak_meninggal: null,
      pilihImunisasi: "",
      jenis_kontrasepsi: "",
      tanggal_penggantian: null,
      penggantian_jenis_kontrasepsi: "",
      keterangan: "",
      completed: false,
      fetchingProvinces: false,
      fetchingRegencies: false,
      fetchingDistricts: false,
      fetchingVillages: false,
      provinces: [],
      regencies: [],
      districts: [],
      villages: [],
      provinceId: null,
      regencyId: null,
      districtId: null,
      villageId: null,
      itemImunisasi: [
        "Imunisasi I",
        "Imunisasi II",
        "Imunisasi III",
        "Imunisasi IV",
        "Imunisasi V",
      ],
    };
  },
  computed: {
    pilihSemuaHasil() {
      return this.pilihImunisasi.length === this.itemImunisasi.length;
    },
    iconHasil() {
      if (this.pilihSemuaHasil) return "mdi-close-box";

      return "mdi-checkbox-blank-outline";
    },
  },
  watch: {
    provinceId() {
      this.regencyId = null;
      this.districtId = null;
      this.villageId = null;
      this.fetchRegencies();
      this.completed = false;
    },
    regencyId() {
      this.districtId = null;
      this.villageId = null;
      this.fetchDistricts();
      this.completed = false;
    },
    districtId() {
      this.villageId = null;
      this.fetchVillages();
      this.completed = false;
    },
    villageId() {
      this.completed = true;
    },
  },
  mounted() {
    this.fetchProvinces();
    this.fetchListOrtu();
  },
  methods: {
    toggleHasil() {
      this.$nextTick(() => {
        if (this.pilihSemuaHasil) {
          this.pilihImunisasi = [];
        } else {
          this.pilihImunisasi = this.itemImunisasi.slice();
        }
      });
    },
    async fetchListOrtu() {
      const response = await axios.get(
        `${config.urlServer}/api/list-orang-tua`,
        {
          headers: {
            Authorization: localStorage.getItem("tokenAuth"),
          },
        },
      );

      this.listOrangTua = response.data;
    },
    async fetchProvinces() {
      this.fetchingProvinces = true;

      const response = await axios.get(
        `${config.urlServer}/api/indonesia/provinces`,
      );

      this.fetchingProvinces = false;
      this.provinces = response.data.map(item => {
        return {
          title: item.name,
          value: item.id,
        };
      });
    },
    async fetchRegencies() {
      if (!this.provinceId) {
        this.regencies = [];

        return;
      }
      this.fetchingRegencies = true;

      const response = await axios.get(
        `${config.urlServer}/api/indonesia/regencies?province_id=${this.provinceId}`,
      );

      this.fetchingRegencies = false;
      this.regencies = response.data.map(item => {
        return {
          title: item.name,
          value: item.id,
        };
      });
    },
    async fetchDistricts() {
      if (!this.regencyId) {
        this.districts = [];

        return;
      }

      this.fetchingDistricts = true;

      const response = await axios.get(
        `${config.urlServer}/api/indonesia/districts?regency_id=${this.regencyId}`,
      );

      this.fetchingDistricts = false;
      this.districts = response.data.map(item => {
        return {
          title: item.name,
          value: item.id,
        };
      });
    },
    async fetchVillages() {
      if (!this.districtId) {
        this.villages = [];

        return;
      }

      this.fetchingVillages = true;

      const response = await axios.get(
        `${config.urlServer}/api/indonesia/villages?district_id=${this.districtId}`,
      );

      this.fetchingVillages = false;
      this.villages = response.data.map(item => {
        return {
          title: item.name,
          value: item.id,
        };
      });
    },

    async submitData() {
      this.isLoading = true;
      let tempat_tinggal = null;
      if (this.completed) {
        tempat_tinggal = `${this.provinces.find(item => item.value === this.provinceId).title
          }, ${this.regencies.find(item => item.value === this.regencyId).title
          }, ${this.districts.find(item => item.value === this.districtId).title
          }, ${this.villages.find(item => item.value === this.villageId).title
          }`;
      } else if (!this.data_tersedia) {
        await Swal.fire({
          toast: true,
          position: "top",
          iconColor: "white",
          color: "white",
          background: "rgb(var(--v-theme-error))",
          showConfirmButton: false,
          timerProgressBar: true,
          timer: 4000,
          icon: "error",
          title: "Data alamat tidak lengkap!!",
        });
        this.isLoading = false;

        return 0;
      }

      try {
        const data = {
          id_orang_tua: this.data_tersedia ? this.pilihOrangTua : null,
          nama_ayah: this.nama_ayah,
          nik_ayah: this.nik_ayah,
          nama_ibu: this.nama_ibu,
          nik_ibu: this.nik_ibu,
          tanggal_meninggal_ibu: this.tanggal_meninggal_ibu,
          no_telp: this.no_telp,
          rt_rw: this.rt_rw,
          tempat_tinggal: tempat_tinggal,
          umur: this.umur,
          tahapan_ks: this.tahapan_ks,
          kelompok_dasawisma: this.kelompok_dasawisma,
          lila: this.lila,
          jumlah_anak_hidup: this.jumlah_anak_hidup,
          jumlah_anak_meninggal: this.jumlah_anak_meninggal,
          imunisasi: this.pilihImunisasi,
          jenis_kontrasepsi: this.jenis_kontrasepsi,
          tanggal_penggantian: this.tanggal_penggantian,
          penggantian_jenis_kontrasepsi: this.penggantian_jenis_kontrasepsi,
          keterangan: this.keterangan,
        };

        const response = await axios.post(
          `${config.urlServer}/api/format-c`,
          data,
          {
            headers: {
              Authorization: localStorage.getItem("tokenAuth"),
            },
          },
        );

        if (response.data.success) {
          Swal.fire({
            toast: true,
            position: "top",
            iconColor: "white",
            color: "white",
            background: "rgb(var(--v-theme-success))",
            showConfirmButton: false,
            timerProgressBar: true,
            timer: 1500,
            icon: "success",
            title: response.data.success.message,
          });
          this.resetData();
          this.fetchListOrtu();
        }
      } catch (error) {
        console.log(error);
        Swal.fire({
          toast: true,
          position: "top",
          iconColor: "white",
          color: "white",
          background: "rgb(var(--v-theme-error))",
          showConfirmButton: false,
          timerProgressBar: true,
          timer: 4000,
          icon: "error",
          title: "Data Tidak Lengkap",
        });
      }
      this.isLoading = false;
    },

    resetData() {
      this.data_tersedia = true;
      this.pilihOrangTua = null;
      this.nama_ayah = "";
      this.nik_ayah = "";
      this.nama_ibu = "";
      this.nik_ibu = "";
      this.tanggal_meninggal_ibu = null;
      this.no_telp = "";
      this.rt_rw = "";
      this.tahapan_ks = "";
      this.kelompok_dasawisma = "";
      this.lila = "";
      this.jumlah_anak_hidup = null;
      this.jumlah_anak_meninggal = null;
      this.pilihImunisasi = "";
      this.jenis_kontrasepsi = "";
      this.tanggal_penggantian = null;
      this.penggantian_jenis_kontrasepsi = "";
      this.keterangan = "";
      this.completed = false;
      this.fetchingProvinces = false;
      this.fetchingRegencies = false;
      this.fetchingDistricts = false;
      this.fetchingVillages = false;
    },
  },
};
</script>
