<template>
  <VRow v-if="isUpload" style="
      position: fixed;
      z-index: 1;
      right: 50px;
      bottom: 50px;
      background-color: azure;
      padding: 1rem;
      padding-block: 5px;
      border-radius: 5px;
    ">
    <Font>Menyimpan... </Font>
    <VProgressCircular indeterminate color="primary" class="ml-3 float-center">
    </VProgressCircular>
  </VRow>
  <VRow v-if="isDelete" style="
      position: fixed;
      z-index: 1;
      right: 50px;
      bottom: 50px;
      background-color: azure;
      padding: 1rem;
      padding-block: 5px;
      border-radius: 5px;
    ">
    <Font>Menghapus... </Font>
    <VProgressCircular indeterminate color="primary" class="ml-3 float-center">
    </VProgressCircular>
  </VRow>
  <VRow>
    <VCol v-if="isLoading" cols="12" md="12" sm="12" class="text-center">
      <VProgressCircular indeterminate color="primary" class="mt-5" size="50"></VProgressCircular>
    </VCol>
    <!-- 👉 Popular Uses Of The Internet -->
    <VCol v-for="(data, index) in dataEvents" v-else :key="index" cols="12" md="4" sm="12">
      <VCard>
        <VImg :src="data.gambar" cover style="height: 280px">
          <h2 v-if="data.gambar == imagePath + null" class="text-center text-secondary" style="margin-top: 25%">
            Tidak Ada Foto
          </h2>
        </VImg>

        <VCardItem>
          <VCardTitle>{{ data.judul }}</VCardTitle>
          <sup>{{ data.tanggal }}</sup>
        </VCardItem>

        <VCardText>
          <p class="mb-4">
            {{ data.overview }}
          </p>
          <p>penulis: {{ data.nama_lengkap }}</p>

          <VRow justify="center">
            <VDialog v-model="dialog[data.id_berita]" persistent width="1024">
              <template #activator="{ props }">
                <VRow class="mt-3">
                  <VCol>
                    <!-- <VBtn> Lihat </VBtn> -->

                    <VBtn color="primary" class="mx-3" v-bind="props">
                      Edit & Lihat
                    </VBtn>
                    <VBtn color="error" class="float-right" @click="deleteEvents(data.id_berita)">
                      <VIcon>bx-trash</VIcon>
                    </VBtn>
                  </VCol>
                </VRow>
              </template>
              <VCard>
                <VCardTitle>
                  <div class="text-h5">Edit</div>
                </VCardTitle>
                <VCardText>
                  <VContainer>
                    <!-- <p class="">Perubahan Otomatis Tersimpan</p> -->
                    <VRow>
                      <VCol cols="12">
                        <VTextField id="judul" v-model="data.judul" placeholder="Masukkan Judul"
                          persistent-placeholder />
                      </VCol>
                      <VCol cols="12">
                        <VTextarea id="deskripsi" v-model="data.deskripsi" placeholder="Masukkan Isi Materi"
                          persistent-placeholder />
                      </VCol>
                      <VCol cols="12">
                        <VTextField id="tanggal" v-model="data.tanggal_pelaksanaan" type="date"
                          placeholder="Masukkan tanggal" persistent-placeholder />
                      </VCol>
                      <VCol cols="12">
                        <!-- <VImg :src="urlServer + data.gambar" :width="110" /> -->
                      </VCol>
                      <VCol cols="12" md="9">
                        <div class="d-flex flex-column justify-center gap-5">
                          <div class="d-flex flex-wrap gap-2">
                            <VBtn id="gambar" color="primary" @click="inputGambar">
                              <VIcon icon="bx-cloud-upload" class="d-sm-none" />
                              <span class="d-none d-sm-block">Upload foto baru</span>
                            </VBtn>

                            <input id="inputGambar" type="file" name="file" accept=".jpeg,.png,.jpg" hidden
                              @change="changeAvatar($event, index)" />
                          </div>
                        </div>
                        <VAvatar v-show="data.gambar !== ''" rounded="lg" size="200" class="me-1 mt-3"
                          :image="data.gambar" />
                      </VCol>
                    </VRow>
                  </VContainer>
                </VCardText>
                <VCardActions>
                  <VSpacer></VSpacer>
                  <VBtn color="blue-darken-1" variant="text" @click="dialog[data.id_berita] = false">
                    Tutup
                  </VBtn>
                  <VBtn color="blue-darken-1" variant="text" @click="
                    putData(index);
                  dialog[data.id_berita] = false;
                  ">
                    Simpan
                  </VBtn>
                </VCardActions>
              </VCard>
            </VDialog>
          </VRow>
        </VCardText>
      </VCard>
    </VCol>
  </VRow>
  <VRow>
    <VCol>
      <div class="text-center my-3 float-right">
        <VPagination v-model="page" :length="banyakPage" :total-visible="6" @click="fetchData"></VPagination>
      </div>
    </VCol>
  </VRow>
</template>

<script>
import axios from "axios";
import config from "@/@core/config";
import Swal from "sweetalert2";

export default {
  data() {
    return {
      dialog: [],
      dataEvents: [],
      urlServer: config.urlServer,
      imagePath: config.imagePath,
      page: 1,
      banyakPage: 0,
      isLoading: false,
      isUpload: false,
      isDelete: false,
    };
  },

  mounted() {
    this.fetchData();
  },

  methods: {
    async putData(indexEvents) {
      this.isUpload = true;
      try {
        const data = {
          id_berita: this.dataEvents[indexEvents].id_berita,
          judul: this.dataEvents[indexEvents].judul,
          tanggal_pelaksanaan: this.dataEvents[indexEvents].tanggal_pelaksanaan,
          deskripsi: this.dataEvents[indexEvents].deskripsi,
        };

        const response = await axios.put(`${this.urlServer}/api/berita`, data, {
          headers: {
            Authorization: localStorage.getItem("tokenAuth"),
          },
        });

        this.isUpload = false;
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
      } catch (error) {
        Swal.fire({
          toast: true,
          position: "top",
          iconColor: "white",
          color: "white",
          background: "rgb(var(--v-theme-error))",
          showConfirmButton: false,
          timerProgressBar: true,
          timer: 5000,
          icon: "error",
          title:
            "Judul dan Tanggal Pelaksanaan Tidak Boleh Kosong atau Judul Sama Seperti Yang Sudah Ada",
        });
      }
      this.isUpload = false;
    },

    inputGambar() {
      document.getElementById("inputGambar").click();
    },

    async fetchData() {
      this.isLoading = true;

      const banyakDataTampil = 6;

      const response = await axios.get(
        `${this.urlServer}/api/berita?start=${this.page}&length=${banyakDataTampil}`,
      );

      this.isLoading = false;

      this.dataEvents = response.data.berita.map(item => {
        item.gambar = this.imagePath + item.gambar;

        return item;
      });
      this.banyakPage = Math.ceil(response.data.jumlah_data / banyakDataTampil);
    },

    async deleteEvents(id_berita) {
      const ask = await Swal.fire({
        title: "Anda yakin ingin menghapus?",
        showConfirmButton: false,
        showDenyButton: true,
        showCancelButton: true,
        denyButtonText: "Hapus",
      });

      if (ask.isDenied) {
        this.isDelete = true;

        const response = await axios.delete(
          `${this.urlServer}/api/berita?id_berita=${id_berita}`,
          {
            headers: {
              Authorization: localStorage.getItem("tokenAuth"),
            },
          },
        );

        this.isDelete = false;
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
          this.fetchData();
        }
      }
    },

    async changeAvatar(file, indexEvents) {
      const files = file.target.files[0];
      if (files) {
        const fileReader = new FileReader();

        // Validasi tipe file sebelum menampilkan gambarnya
        if (
          files.type === "image/jpeg" ||
          files.type === "image/png" ||
          files.type === "image/jpg"
        ) {
          fileReader.readAsDataURL(files);
          fileReader.onload = async () => {
            if (typeof fileReader.result === "string") {
              this.dataEvents[indexEvents].gambar = fileReader.result;

              const response = await axios.put(
                `${this.urlServer}/api/berita`,
                {
                  id_berita: this.dataEvents[indexEvents].id_berita,
                  gambar: fileReader.result,
                },
                {
                  headers: {
                    Authorization: localStorage.getItem("tokenAuth"),
                  },
                },
              );
            }
          };
        } else {
          // Tindakan jika tipe file tidak valid
          alert("File harus berupa gambar dengan tipe jpeg, png, atau jpg.");
          resetAvatar();
        }
      }
    },
  },
};
</script>
