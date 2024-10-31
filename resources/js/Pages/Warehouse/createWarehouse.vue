<template>
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h1>Добавить склад</h1>
                <router-link to="/warehouses" class="btn btn-secondary">Назад</router-link>
            </div>
        </div>
        <div class="card-body">
            <form action="">
                <div class="form-group">
                    <label for="name">Название;</label>
                    <input type="text" name="name" id="name" class="form-control" v-model="warehouse.name" />
                </div>
                <button type="button" class="btn btn-secondary mt-2" v-on:click="saveWarehouse()">Сохранить</button>
            </form>
        </div>
    </div>
</template>

<script setup>
    import { ref } from 'vue';
    import axios from '../config/axios.js';
    import toastr from 'toastr';

    const warehouse = ref({});
    const errors = ref({});

    const saveWarehouse = async () => {
        try {
            const res = await axios.post('/api/warehouses/save', warehouse.value);
            toastr.success('Warehouse saved Successfully');
            warehouse.value = {}; // Обновляем состояние
        } catch (error) {
            errors.value = error.response.data.errors;

            // Выводим сообщения об ошибках
            for (const key in errors.value) {
                toastr.error(errors.value[key]);
            }
        }
    };

</script>





