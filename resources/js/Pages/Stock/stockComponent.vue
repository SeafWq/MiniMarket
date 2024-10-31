<template>
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h1>Товары</h1>
                <div>
                    <label for="perPage">Товаров на странице:</label>
                    <input type="number" id="perPage" v-model.number="perPage" min="1">
                </div>
            </div>
        </div>

        <div class="card-body">
            <table class="table">
                <thead>
                <tr>
                    <th scope="col" @click="sort('stocks.product.name')">#</th>
                    <th scope="col" @click="sort('product.name')">Продукт</th>
                    <th scope="col" @click="sort('product.price')">Цена</th>
                    <th scope="col" @click="sort('warehouse.name')">Склад</th>
                    <th scope="col" @click="sort('stock')">Остаток</th>
                </tr>
                </thead>
                <tbody>
                    <tr v-for="(stock, key) in filteredStocks" :key="stock.id">
                        <th scope="row">{{key}}</th>
                        <td>{{stock.product.name}}</td>
                        <td>{{stock.product.price}}</td>
                        <td>{{stock.warehouse.name}}</td>
                        <td>{{stock.stock}}</td>
                    </tr>
                </tbody>
            </table>
            <div>
                <label for="priceFrom">Цена от:</label>
                <input type="number" id="priceFrom" v-model.number="priceFrom">
            </div>

            <div>
                <label for="priceTo">Цена до:</label>
                <input type="number" id="priceTo" v-model.number="priceTo">
            </div>

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
    const stocks = ref([]);
    let currentPage = ref(1);
    let availablePages = ref();
    let perPage = ref(10);
    let sortBy = ref(null); // Хранит поле сортировки
    let sortOrder = ref('asc'); // Хранит порядок сортировки
    let priceFrom = ref(null);
    let priceTo = ref(null);


    const getStocks = async () => {
        try {
            const res = await axios.get(`/api/stocks?page=${currentPage.value}&per_page=${perPage.value}`);
            stocks.value = res.data.stocks.data;
            availablePages.value = res.data.stocks.last_page;
        } catch (error) {
            console.error(error);
        }
    };

    const goToPage = (page) => {
        currentPage.value = page;
        router.push({
            name: 'StockComponent',
            query: { page: page, per_page: perPage.value }, // Передаем perPage в URL
        });
    };

    const sort = (field) => {
        if (sortBy.value === field) {
            sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc';
        } else {
            sortBy.value = field;
            sortOrder.value = 'asc';
        }
    };

    // Вычисляемое свойство для сортировки данных
    const sortedStocks = computed(() => {
        const data = stocks.value;
        if (!sortBy.value) {
            return data; // Если sortBy пустое, то не сортируем
        }

        return data.sort((a, b) => {
            let aValue = a;
            let bValue = b;

            // Разделяем sortBy на части, если есть точка
            const parts = sortBy.value.split('.');
            if (parts.length > 1) {
                aValue = aValue[parts[0]][parts[1]];
                bValue = bValue[parts[0]][parts[1]];
            } else {
                aValue = aValue[sortBy.value];
                bValue = bValue[sortBy.value];
            }

            if (sortOrder.value === 'asc') {
                // Сортируем по возрастанию
                return aValue < bValue ? -1 : aValue > bValue ? 1 : 0;
            } else {
                // Сортируем по убыванию
                return aValue > bValue ? -1 : aValue < bValue ? 1 : 0;
            }
        });
    });


    const filteredStocks = computed(() => {
        const data = sortedStocks.value;

        if (!priceFrom.value && !priceTo.value ) {
            return data;
        }

        return data.filter((stock) => {
            const price = stock.product.price;
            return (
                (!priceFrom.value || price >= priceFrom.value) &&
                (!priceTo.value || price <= priceTo.value)
            );
        });
    });
    // Используем watch для отслеживания изменения currentPage и perPage
    watch([currentPage, perPage, priceFrom, priceTo], () => {
        getStocks();
    });

    onMounted(getStocks);

</script>

<style scoped>

</style>
