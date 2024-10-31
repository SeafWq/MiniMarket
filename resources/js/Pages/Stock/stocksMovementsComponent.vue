<template>
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h1>Товары</h1>
                <div>
                    <label for="perPage">Движений товаров на странице:</label>
                    <input type="number" id="perPage" v-model.number="perPage" min="1">
                </div>
            </div>
        </div>

        <div class="card-body">
            <table class="table">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Продукт</th>
                    <th scope="col">Ушел</th>
                    <th scope="col">Склад</th>
                    <th scope="col">Количество</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(stock, key) in movements" :key="stock.id">
                    <th scope="row">{{key}}</th>
                    <td>{{stock.product.name}}</td>
                    <td>{{stock.movement_type}}</td>
                    <td>{{stock.warehouse.name}}</td>
                    <td>{{stock.quantity}}</td>
                </tr>
                </tbody>
            </table>
        </div>
        <div v-if="availablePages > 1">
            <button @click="goToPage(1)" :disabled="currentPage === 1">Первая</button>
            <button @click="goToPage(currentPage - 1)" :disabled="currentPage === 1">Предыдущая</button>
            <button @click="goToPage(currentPage + 1)" :disabled="currentPage === availablePages">Следующая</button>
            <button @click="goToPage(availablePages)" :disabled="currentPage === availablePages">Последняя</button>
        </div>
    </div>
</template>

<script setup>
    import axios from "../config/axios.js";
    import toastr from "toastr";
    import { ref, onMounted, watch, computed } from 'vue'
    import { useRoute, useRouter } from 'vue-router';

    const router = useRouter();
    const movements = ref([]);
    let currentPage = ref(1);
    let availablePages = ref();
    let perPage = ref(10);


    const getStocks = async () => {
        try {
            const res = await axios.get(`/api/stocks-movements?page=${currentPage.value}&per_page=${perPage.value}`);
            console.log(res)
            movements.value = res.data.movements.data;
            availablePages.value = res.data.movements.last_page;
        } catch (error) {
            console.error(error);
        }
    };

    const goToPage = (page) => {
        currentPage.value = page;
        router.push({
            name: 'StocksMovementsComponent',
            query: { page: page, per_page: perPage.value }, // Передаем perPage в URL
        });
    };

    watch([currentPage, perPage], () => {
        getStocks();
    });

    onMounted(getStocks)
</script>

<style scoped>

</style>
