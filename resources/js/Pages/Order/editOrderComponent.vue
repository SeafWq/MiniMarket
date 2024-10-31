<template>
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h1>Обновить заказ</h1>
                <router-link to="/orders" class="btn btn-secondary">Назад</router-link>
            </div>
        </div>
        <div class="card-body">
            <form @submit.prevent="updateOrder">
                <div class="form-group">
                    <label for="customer">Заказчик</label>
                    <input
                        type="text"
                        name="customer"
                        id="customer"
                        class="form-control"
                        v-model="order.customer"
                    />
                </div>
                <div v-for="(item, index) in order.order_items" :key="index" class="mb-3">
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="product-{{ index }}">Товар</label>
                            <select
                                v-model="item.product.id"
                                id="product-{{ index }}"
                                class="form-control"
                            >
                                <option value="{{item.product.name}}" disabled>Выберите товар</option>
                                <option v-for="product in products" :key="product.id" :value="product.id">
                                    {{ product.name }}
                                </option>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="count-{{ index }}">Количество</label>
                            <input
                                type="number"
                                name="count-{{ index }}"
                                id="count-{{ index }}"
                                class="form-control"
                                v-model.number="item.count"
                                min="1"
                            />
                        </div>
                        <div class="form-group col-md-4">
                            <label for="warehouse-{{ index }}">Откуда заказывать</label>
                            <select
                                v-model="item.warehouse_id"
                                id="warehouse-{{ index }}"
                                class="form-control"
                            >
                                <option value="" disabled>Выберите склад</option>
                                <option
                                    v-for="warehouse in availableWarehouses(item.product_id)"
                                    :key="warehouse.id"
                                    :value="warehouse.id"
                                >
                                    {{ warehouse.name }}
                                </option>
                            </select>
                        </div>
                        <button
                            type="button"
                            class="btn btn-danger mt-2"
                            @click="removeItem(index)"
                        >
                            Удалить
                        </button>
                    </div>
                </div>
                <button type="button" class="btn btn-secondary mt-2" @click="addItem">
                    Добавить товар
                </button>
                <button type="submit" class="btn btn-secondary mt-2">Обновить</button>
            </form>
        </div>
    </div>
</template>


<script setup>
    import { ref, onMounted, computed } from 'vue';
    import axios from 'axios';
    import { useRoute, useRouter } from 'vue-router';

    const router = useRouter();
    const route = useRoute();

    const orderId = ref(parseInt(route.params.id));
    const order = ref({});
    const products = ref([]); // Инициализация как ref([])


    onMounted(() => {
        getOrder();
        getProductsWithWarehouses();
    });

    const getOrder = async () => {
        try {
            const res = await axios.get(`/api/orders/${orderId.value}`);
            order.value = res.data;
            console.log(order.value)
            if (order.value.order_items) {
                order.value.items = order.value.order_items.map((item) => ({
                    ...item,
                    warehouse_id: item.warehouse_id,
                }));
            } else {
                order.value.items = [];
                console.warn("items не найдены в ответе API");
            }
            console.log("order.value:", order.value.order_items);
        } catch (error) {
            console.error(error);
        }
    };

    const availableWarehouses = computed(() => {
        return (productId) => {
            if (!productId) {
                return [];
            }

            const product = products.value.find(p => p.id === productId);
            if (product) {
                return product.stocks.map(stock => stock.warehouse);
            }

            return [];
        };
    });

    const getProductsWithWarehouses = async () => {
        try {
            const res = await axios.get('/api/products');
            products.value = res.data.products;
            console.log(products.value)
        } catch (error) {
            console.error(error);
        }
    };

    const updateOrder = async () => {
        try {
            const res = await axios.put(`/api/orders/${orderId.value}`, {
                customer: order.value.customer,
                items: order.value.order_items.map((item) => ({
                    product_id: item.product_id,
                    count: item.count,
                    warehouse_id: item.warehouse_id,
                })),
            });
            console.log("Заказ обновлен", res.data);
            router.push("/orders");
        } catch (error) {
            console.error(error);
        }
    };

    const addItem = () => {
        if (order.value) {
            order.value.items.push({
                product_id: null,
                count: 1,
                warehouse_id: null,
            });
        } else {
            console.error("order.value не определен");
        }
    };

    const removeItem = (index) => {
        order.value.items.splice(index, 1);
    };


</script>

<style scoped>

</style>
