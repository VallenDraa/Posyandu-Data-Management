<template>
  <VRow>
    <VCol cols="12" md="12">
      <VCol cols="12">
        <VCard>
          <VCardItem>
            <h1 class="text-center">
              <VIcon>{{ dataEdit.bayi.jenis_kelamin == "L" ? "bx-male" : "bx-female" }}</VIcon>Grafik {{
                dataEdit.bayi.jenis_kelamin == "L" ? "Laki Laki" : "Perempuan" }}
            </h1>
            <VueApexCharts type="line" height="530" :options="chartOptions" :series="series" />
          </VCardItem>
        </VCard>
      </VCol>
      <VCol cols="12">
        <VCard title="Data Penimbangan Bayi">
          <VCardText>
            <VRow>
              <VCol>
                <p class="mb-4">
                  Nama:
                  <Font class="float-right">{{ dataEdit.bayi.nama }}</Font>
                </p>
                <hr class="text-primary mb-5" style="border-style: dashed;opacity: 0.5;">
                <p class="mb-4">
                  Nama Ayah:
                  <Font class="float-right">{{ dataEdit.bayi.nama_ayah }}</Font>
                </p>
                <hr class="text-primary mb-5" style="border-style: dashed;opacity: 0.5;">
                <p class="mb-4">
                  Nama Ibu:
                  <Font class="float-right">{{ dataEdit.bayi.nama_ibu }}</Font>
                </p>
                <hr class="text-primary mb-5" style="border-style: dashed;opacity: 0.5;">
                <p class="mb-4">
                  Tanggal Lahir:
                  <Font class="float-right">{{
                    dataEdit.bayi.tanggal_lahir
                    }}</Font>
                </p>
                <hr class="text-primary mb-5" style="border-style: dashed;opacity: 0.5;">
                <p class="mb-4">
                  Jenis Kelamin:<Font class="float-right">
                    {{ dataEdit.bayi.jenis_kelamin == "L" ? "Laki Laki" : "Perempuan" }}
                  </Font>
                </p>
                <hr class="text-primary mb-5" style="border-style: dashed;opacity: 0.5;">
                <p class="mb-4">
                  BB Saat Lahir :<Font class="float-right">
                    {{
                      dataEdit.bayi.berat_lahir
                        ? dataEdit.bayi.berat_lahir + " KG"
                        : "-"
                    }}
                  </Font>
                </p>
              </VCol>
              <VCol cols="12">
                <VDialog v-model="dialog" persistent width="1024">
                  <template #activator="{ props }">
                    <VBtn color="primary" class="ml-2" v-bind="props" prepend-icon="bx-edit">
                      Keterangan
                    </VBtn>
                  </template>
                  <VCard>
                    <VCardTitle>
                      <span class="text-h5">Ubah Data</span>
                    </VCardTitle>
                    <VCardItem>
                      <VContainer>
                        <VRow>
                          <VCol cols="12">
                            <VSelect v-model="pilihHasil" :items="itemHasil" label="Hasil penimbangan"
                              placeholder="Pilih beberapa" multiple chips>
                              <template #prepend-item>
                                <VListItem ripple @mousedown.prevent @click="toggleHasil">
                                  <VListItemAction>
                                    <VIcon :color="pilihHasil.length === itemHasil.length ? 'primary' : 'secondary'">
                                      {{ iconHasil }}
                                    </VIcon>
                                    <Font class="ml-2"> Pilih Semua </Font>
                                  </VListItemAction>
                                </VListItem>
                                <VDivider class="mt-2"></VDivider>
                              </template>
                            </VSelect>
                          </VCol>
                          <VCol cols="12">
                            <VSelect v-model="pilihPelayanan" :items="itemPelayanan" label="Pelayanan yang diberikan"
                              placeholder="Pilih beberapa" multiple chips>
                              <template #prepend-item>
                                <VListItem ripple @mousedown.prevent @click="togglePelayanan">
                                  <VListItemAction>
                                    <VIcon
                                      :color="pilihPelayanan.length === itemPelayanan.length ? 'primary' : 'secondary'">
                                      {{ iconPelayanan }}
                                    </VIcon>
                                    <Font class="ml-2"> Pilih Semua </Font>
                                  </VListItemAction>
                                </VListItem>
                                <VDivider class="mt-2"></VDivider>
                              </template>
                            </VSelect>
                          </VCol>
                        </VRow>
                      </VContainer>
                    </VCardItem>
                    <VCardActions>
                      <VSpacer></VSpacer>
                      <VBtn color="secondary" variant="text" @click="dialog = false">
                        Tutup
                      </VBtn>
                      <VBtn color="primary" variant="text" @click="
                        submitKeterangan();
                      dialog = false;
                      ">
                        Simpan
                      </VBtn>
                    </VCardActions>
                  </VCard>
                </VDialog>
              </VCol>
              <VCol v-for="(item, index) in dataEdit.penimbangan" :key="index" cols="12">
                <hr class="mb-5" style="opacity: 0.3;">
                <h4 class="my-5">{{ item.judul }}</h4>
                <VRow>
                  <VCol cols="12" sm="12" md="6" lg="6">
                    <VTextField v-model="dataEdit.penimbangan[index].berat_badan" type="number" label="Berat Badan"
                      placeholder="Masukkan Berat Badan" :rules="[
                        (input) => (input >= 0) || 'Berat badan tidak bisa negatif'
                      ]" :error-messages="errorField.includes(index) ? errorFieldMessage : ''" />
                  </VCol>
                  <VCol v-if="index <= 6" cols="12" sm="12" md="6" lg="6">
                    <VSelect v-model="dataEdit.penimbangan[index].asi_eksklusif" :error="errorField.includes(index)"
                      label="Asi Eksklusif" placeholder="Masukkan Asi Ekslusif" :items="['Ya', 'Tidak', 'Alpa']" />
                  </VCol>
                  <VCol cols="12" sm="12" md="6" lg="6">
                    <VTextField v-model="dataEdit.penimbangan[index].lila" type="number" label="Lila"
                      placeholder="Masukkan Lila" :rules="[
                        (input) => (input >= 0) || 'Lila tidak bisa negatif'
                      ]" :error-messages="errorField.includes(index) ? errorFieldMessage : ''" />
                  </VCol>
                  <VCol cols="12" sm="12" md="6" lg="6">
                    <VTextField v-model="dataEdit.penimbangan[index].lingkar_kepala" type="number"
                      label="Lingkar Kepala" placeholder="Masukkan Lingkar Kepala" :rules="[
                        (input) => (input >= 0) || 'Lingkar Kepala tidak bisa negatif'
                      ]" :error-messages="errorField.includes(index) ? errorFieldMessage : ''" />
                  </VCol>
                  <VCol cols="12" sm="12" md="6" lg="6">
                    <VTextField v-model="dataEdit.penimbangan[index].tinggi_badan" type="number" label="Tinggi Badan"
                      placeholder="Masukkan Tinggi Badan" :rules="[
                        (input) => (input >= 0) || 'Tinggi badan tidak bisa negatif'
                      ]" :error-messages="errorField.includes(index) ? errorFieldMessage : ''" />
                  </VCol>
                  <VCol cols="12" sm="12" md="6" lg="6">
                    <VSelect v-model="dataEdit.penimbangan[index].cara_ukur" label="Cara Ukur" placeholder="Cara ukur"
                      :items="['Berdiri', 'Telentang']" />
                  </VCol>
                  <VCol v-if="index > 5" cols="12" sm="12" md="6" lg="6">
                    <VCheckbox v-model="dataEdit.penimbangan[index].vit_a" label="Mendapatkan Vitamin A" />
                  </VCol>

                  <VCol cols="12" sm="12" md="12">
                    <VBtn class="text-none text-subtitle-1" variant="tonal" :color="getColorNTOB(item.ntob)"
                      style="width: 100%;">
                      {{
                        dataEdit.penimbangan[index].ntob || "Kosong" }}</VBtn>
                  </VCol>
                </VRow>
              </VCol>
              <VCol cols=" 12" class="d-flex gap-4">

                <VBtn :id="index" :disabled="isLoading[index]" type="submit" style="width: 100%;" @click="submitData">
                  <VProgressCircular v-if="isLoading[index]" indeterminate color="white" :for="index">
                  </VProgressCircular>

                  <Font v-else>
                    simpan
                  </Font>
                </VBtn>
              </VCol>
            </VRow>
          </VCardText>
        </VCard>
      </VCol>
    </VCol>
  </VRow>
</template>

<script>
import VueApexCharts from "vue3-apexcharts";
import axios from "axios";
import config from "@/@core/config";
import Swal from "sweetalert2";

export default {
  components: {
    VueApexCharts,
  },
  data() {
    return {
      dialog: false,
      pilihHasil: [],
      itemHasil: [
        'HB-0 24 JAM',
        'Vitamin',
        'BCG',
        'POLIO 1',
        'POLIO 2',
        'POLIO 3',
        'POLIO 4',
        'DPT-HB-HiB 1',
        'DPT-HB-HiB 2',
        'DPT-HB-HiB 3',
        'DPT-HB-HiB 4',
        'Inactivated Pollo Vaccine (IPV)',
        'Campak Rubella',
      ],
      pilihPelayanan: [],
      itemPelayanan: [
        'VIT A I',
        'VIT A II',
        'DPT-HB-HiB Lanjutan',
        'Campak Rubella Lanjutan',
        'Makanan Tambahan',
        'ORALIT',
      ],
      errorField: [],
      errorFieldMessage: '',
      isLoading: false,
      series: [],
      chartOptions: {
        chart: {
          height: 350,
          type: "line",
          zoom: {
            enabled: false,
          },
        },
        toolBar: {
          tools: {
            download: '<img src="/static/icons/download.png" class="ico-download" width="20">',
          },
        },
        colors: [
          "#FF5252", // rgb(var(--v-theme-error))
          "#FFC107", // rgb(var(--v-theme-warning))
          "#4CAF50", // rgb(var(--v-theme-success))
          "#4CAF50", // rgb(var(--v-theme-success))
          "#4CAF50", // rgb(var(--v-theme-success))
          "#FFC107", // rgb(var(--v-theme-warning))
          "#FF5252", // rgb(var(--v-theme-error))
          "#696CFF",
        ],
        markers: {
          size: 1,
        },
        xaxis: {
          title: {
            text: "Umur Bayi (bulan)",
          },
          categories: this.categories,
          labels: {
            formatter: value => {
              return `${value - 1}`
            },
          },
        },
        markers: {
          size: 6,
        },
        yaxis: {
          title: {
            text: "Berat Badan (Kg)",
          },
          min: 0,
        },
        legend: {
        },
      },
      dataEdit: {
        bayi: {
          nama: "",
          tanggal_lahir: "",
          jenis_kelamin: "",
          berat_lahir: null,
          keterangan: null,
        },
        penimbangan: [],
      },
    };
  },
  computed: {
    pilihSemuaHasil() {
      return this.pilihHasil.length === this.itemHasil.length
    },
    iconHasil() {
      if (this.pilihSemuaHasil) return 'mdi-close-box'

      return 'mdi-checkbox-blank-outline'
    },
    pilihSemuaPelayanan() {
      return this.pilihPelayanan.length === this.itemPelayanan.length
    },
    iconPelayanan() {
      if (this.pilihSemuaPelayanan) return 'mdi-close-box'

      return 'mdi-checkbox-blank-outline'
    },
  },
  mounted() {
    this.fetchData();
  },
  methods: {
    toggleHasil() {
      this.$nextTick(() => {
        if (this.pilihSemuaHasil) {
          this.pilihHasil = []
        } else {
          this.pilihHasil = this.itemHasil.slice()
        }
      })
    },
    togglePelayanan() {
      this.$nextTick(() => {
        if (this.pilihSemuaPelayanan) {
          this.pilihPelayanan = []
        } else {
          this.pilihPelayanan = this.itemPelayanan.slice()
        }
      })
    },
    getColorNTOB(ntob) {
      if (ntob) {
        return ntob === 'Kosong' ? 'secondary' :
          ntob[0] === 'T' ? 'warning' :
            ntob.substring(0, 3) === 'BGM' ? 'error' : 'primary';
      } else {
        // Jika ntob null, mungkin Anda ingin mengembalikan warna default di sini
        return 'default-color';
      }
    },
    async fetchData() {
      // Membuat objek URLSearchParams dari query string
      const queryString = window.location.search;
      const queryParams = new URLSearchParams(queryString);

      // Mendapatkan nilai dari parameter tertentu
      if (queryParams.get("id_bayi")) {
        const idBayi = queryParams.get("id_bayi");

        const response = await axios.get(
          `${config.urlServer}/api/format-ba?id_bayi=${idBayi}`,
          {
            headers: {
              Authorization: localStorage.getItem("tokenAuth"),
            },
          },
        );

        this.pilihHasil = response.data.hasil_penimbangan;
        this.pilihPelayanan = response.data.pelayanan;
        this.series = response.data.series;
        this.dataEdit = response.data;
        for (let i = 0; i < this.dataEdit.length; i++) {
          this.isLoading[i] = false;
        }
        console.log(response.data.penimbangan)
      } else {
        window.location.href = "/admin/dashboard";
      }
    },
    async submitKeterangan() {
      try {

        const queryString = window.location.search;
        const queryParams = new URLSearchParams(queryString);

        // Mendapatkan nilai dari parameter tertentu
        if (queryParams.get("id_bayi")) {
          const idBayi = queryParams.get("id_bayi");

          const data = {
            id_bayi: idBayi,
            hasil_penimbangan: this.pilihHasil,
            pelayanan: this.pilihPelayanan,
          }

          const response = await axios.put(
            `${config.urlServer}/api/format-ba`,
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
          }
        }

      } catch (get) {
        const errorMessage = Object.values(get.response.data.errors).join(
          " - ",
        );

        Swal.fire({
          toast: true,
          position: "top",
          iconColor: "white",
          color: "white",
          background: "rgb(var(--v-theme-error))",
          showConfirmButton: false,
          timerProgressBar: true,
          timer: 2000,
          icon: "error",
          title: errorMessage,
        });
        this.isLoading = false;
      }
    },
    async submitData() {

      this.isLoading = true;

      try {

        const queryString = window.location.search;
        const queryParams = new URLSearchParams(queryString);
        const idBayi = queryParams.get("id_bayi");

        const data = {
          penimbangan: this.dataEdit.penimbangan,
          id_bayi: idBayi,
        };

        const response = await axios.post(
          `${config.urlServer}/api/format-ba`,
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
          this.errorField = [];
        } else {
          Swal.fire({
            toast: true,
            position: "top",
            iconColor: "white",
            color: "white",
            background: "rgb(var(--v-theme-warning))",
            showConfirmButton: false,
            timerProgressBar: true,
            timer: 4000,
            icon: "warning",
            title: response.data.warning.message,
          });
          this.errorFieldMessage = response.data.warning.message;
          this.errorField = response.data.error_field;
        }
        this.fetchData();

      } catch (get) {
        const errorMessage = Object.values(get.response.data.errors).join(
          " - ",
        );

        Swal.fire({
          toast: true,
          position: "top",
          iconColor: "white",
          color: "white",
          background: "rgb(var(--v-theme-error))",
          showConfirmButton: false,
          timerProgressBar: true,
          timer: 2000,
          icon: "error",
          title: errorMessage,
        });
        this.isLoading = false;
      }
    },
  },
};
</script>
