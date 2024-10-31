<template>
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h1>Создать заказ</h1>
                <router-link to="/warehouses" class="btn btn-secondary">Назад</router-link>
            </div>
        </div>
        <div class="card-body">
            <form @submit.prevent="saveOrder">
                <div class="form-group">
                    <label for="customer">Заказчик</label>
                    <input type="text" name="customer" id="customer" class="form-control" v-model="order.customer" />
                </div>
                <div v-for="(item, index) in order.items" :key="index" class="mb-3">
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="product-{{ index }}">Товар</label>
                            <select v-model="item.product_id" id="product-{{ index }}" class="form-control">
                                <option value="" disabled>Выберите товар</option>
                                <option v-for="product in products" :key="product.id" :value="product.id">
                                    {{ product.name }}
                                </option>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="count-{{ index }}">Количество</label>
                            <input type="number" name="count-{{ index }}" id="count-{{ index }}" class="form-control" v-model.number="item.count" min="1" />
                        </div>
                        <div class="form-group col-md-4">
                            <label for="warehouse-{{ index }}">Откуда заказывать</label>
                            <select v-model="item.warehouse_id" id="warehouse-{{ index }}" class="form-control">
                                <option value="" disabled>Выберите склад</option>
                                <option v-for="warehouse in availableWarehouses(item.product_id)" :key="warehouse.id" :value="warehouse.id">
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
                <button type="button" class="btn btn-secondary mt-2" @click="addItem">Добавить товар</button>
                <button type="submit" class="btn btn-secondary mt-2">Создать</button>
            </form>
        </div>
    </div>
</template>

<script setup>
    import { ref, onMounted, watch, computed } from 'vue';
    import axios from 'axios';
    import { useRoute, useRouter } from 'vue-router';
    import toastr from "toastr";

    const router = useRouter();

    const order = ref({
        customer: '',
        items: []
    });

    const products = ref([]);
    const selectedProduct = ref(null);

    const warehouses = ref([]);
    const selectedWarehouse = ref(null);

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

    const saveOrder = async () => {
        try {
            const res = await axios.post('/api/order-create', {
                customer: order.value.customer,
                items: order.value.items.map(item => ({
                    product_id: item.product_id,
                    count: item.count,
                    warehouse_id: item.warehouse_id
                }))
            });
            // Обработка успешного создания заказа
            console.log('Заказ создан', res.data);
            // Переход на страницу заказов
            router.push('/orders');
        } catch (error) {
            // Обработка ошибок
            console.error(error);
        }
    };

    const addItem = () => {
        order.value.items.push({
            product_id: null,
            count: 1,
            warehouse_id: null
        });
    };
    const removeItem = (index) => {
        order.value.items.splice(index, 1);
    };

    onMounted(() => {
        getProductsWithWarehouses();
    });

</script>
