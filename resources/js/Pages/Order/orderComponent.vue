<template>
    <div>
        <div class="container mt-5">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h1>Заказы</h1>
                                <div>
                                    <label for="perPage">Заказов на странице:</label>
                                    <input type="number" id="perPage" v-model.number="perPage" min="1">
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Заказчик</th>
                                    <th scope="col">Статус</th>
                                    <th scope="col">Товары</th>
                                    <th scope="col">Сумма заказа</th>
                                    <th scope="col">Действие</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(order, key) in orders" :key="order.id">
                                    <th scope="row">{{ key + 1 }}</th>
                                    <td>{{ order.customer }}</td>
                                    <td>{{ order.status }}</td>
                                    <td>
                                        <ul>
                                            <li v-for="orderItem in order.order_items" :key="orderItem.id">
                                                {{ orderItem.product.name }} ({{ orderItem.count }}) - {{ (orderItem.product.price * orderItem.count).toFixed(2) }}₽
                                            </li>
                                        </ul>
                                    </td>
                                    <td>{{ totalSum(order).toFixed(2) }}₽</td>
                                    <td>
                                        <button
                                            type="button"
                                            class="btn btn-info"
                                            :disabled="order.status === 'completed' || order.status === 'canceled'"
                                        >
                                            <router-link
                                                :to="{ name: 'EditOrderComponent', params: { id: order.id } }"
                                                :disabled="order.status === 'completed' || order.status === 'canceled'"
                                            >
                                                Изменить
                                            </router-link>
                                        </button>

                                        <button
                                            type="button"
                                            class="btn btn-danger ms-2"
                                            v-on:click="cancelOrder(order.id)"
                                            :disabled="order.status === 'completed' || order.status === 'canceled'"
                                        >
                                            Отменить
                                        </button>
                                        <button
                                            type="button"
                                            class="btn btn-dark ms-2"
                                            v-on:click="resumeOrder(order.id)"
                                            :disabled="order.status !== 'canceled'"
                                        >
                                            Возобновить
                                        </button>
                                        <button
                                            type="button"
                                            class="btn btn-outline-success ms-2"
                                            v-on:click="completeOrder(order.id)"
                                            :disabled="order.status === 'completed'"
                                        >
                                            Завершить
                                        </button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>

                            <div>
                                <label for="statusFilter">Статус:</label>
                                <select id="statusFilter" v-model="statusFilter">
                                    <option value="">Все</option>
                                    <option value="active">Активный</option>
                                    <option value="completed">Завершен</option>
                                    <option value="canceled">Отменен</option>
                                </select>
                            </div>

                            <div>
                                <label for="customerFilter">Клиент:</label>
                                <input type="text" id="customerFilter" v-model="customerFilter">
                            </div>

                            <div v-if="availablePages > 1">
                                <button @click="goToPage(1)" :disabled="currentPage === 1">Первая</button>
                                <button @click="goToPage(currentPage - 1)" :disabled="currentPage === 1">Предыдущая</button>
                                <button @click="goToPage(currentPage + 1)" :disabled="currentPage === availablePages">Следующая</button>
                                <button @click="goToPage(availablePages)" :disabled="currentPage === availablePages">Последняя</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


<script setup>
        import { ref, onMounted, watch, computed } from 'vue';
        import axios from 'axios';
        import { useRoute, useRouter } from 'vue-router';
        import toastr from "toastr";

        const router = useRouter();
        const orders = ref([]);
        let currentPage = ref(1);
        let availablePages = ref();
        let perPage = ref(10);
        let statusFilter = ref('');
        let customerFilter = ref('');

        const getOrders = async () => {
            try {
                const queryParams = { page: currentPage.value, per_page: perPage.value };
                if (statusFilter.value) {
                    queryParams.status = statusFilter.value;
                }
                if (customerFilter.value) {
                    queryParams.customer = customerFilter.value;
                }

                const res = await axios.get(`/api/orders?page=${currentPage.value}&per_page=${perPage.value}`);
                orders.value = res.data.data;
                availablePages.value = res.data.last_page;
            } catch (error) {
                console.error(error);
            }
        };

        const cancelOrder = async (orderId) => { // Изменили аргумент
            try {
                const res = await axios.delete(`/api/orders-cancel/${orderId}`); // Используем orderId
                toastr.success(res.data.message);
                getOrders();
            } catch (error) {
                console.error(error);
            }
        }

        const resumeOrder = async (orderId) => { // Изменили аргумент
            try {
                const res = await axios.put(`/api/orders-resume/${orderId}`); // Используем orderId
                toastr.success(res.data.message);
                getOrders();
            } catch (error) {
                console.error(error);
            }
        }

        const completeOrder = async (orderId) => { // Изменили аргумент
            try {
                const res = await axios.put(`/api/orders-complete/${orderId}`); // Используем orderId
                toastr.success(res.data.message);
                getOrders();
            } catch (error) {
                console.error(error);
            }
        }

        const goToPage = (page) => {
            currentPage.value = page;
            router.push({
                name: 'OrderComponent',
                query: { page: page, per_page: perPage.value }, // Передаем perPage в URL
            });
        };

        // Используем watch для отслеживания изменения currentPage и perPage
        watch([currentPage, perPage, statusFilter, customerFilter], () => {
            getOrders();
        });

        const totalSum = (order) => {
            return order.order_items.reduce((sum, orderItem) => {
                return sum + orderItem.product.price * orderItem.count;
            }, 0);
        };

        onMounted(getOrders);
</script>


<style scoped>

</style>
