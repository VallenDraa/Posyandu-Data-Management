<script setup>
import { ref } from "vue"
import debounce from "just-debounce";
import Swal from "sweetalert2";
import { getErrorMessage } from "@/utils/get-error-message";
import { getPinnedProduk } from "../../api/produk-api";
import CardProduk from "@/components/cards/card-produk.vue";

const listProdukItems = ref([]);
const isSearching = ref(true);

const searchListProduk = async () => {
	try {
		isSearching.value = true;

		const data = await getPinnedProduk({
		})

		listProdukItems.value = data;
	} catch (error) {
		await Swal.fire({
			icon: 'error',
			title: 'Gagal mengambil data produk!',
			text: getErrorMessage(error, 'Gagal mengambil data produk!'),
		})
	} finally {
		isSearching.value = false;
	}
}

const debouncedSearchListProduk = debounce(searchListProduk, 350)

onMounted(searchListProduk)
</script>

<template>
	<VRow v-if="!isSearching">
		<VCol v-for="(produk, index) in listProdukItems" :key="index" cols="12" sm="6" md="4">
			<CardProduk :to="`/admin/layanan/si-binggo/produk/${produk.id}`" :data-produk="produk" />
		</VCol>
		<VCol v-if="listProdukItems.length === 0">
			<p class="text-center">Belum ada produk atau tidak ada hasil yang sesuai.</p>
		</VCol>
	</VRow>
	<VRow v-else>
		<VProgressCircular indeterminate color="primary" class="mt-5 mx-auto" size="50">
		</VProgressCircular>
	</VRow>
</template>