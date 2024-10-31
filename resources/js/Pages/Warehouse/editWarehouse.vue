<template>
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h1>Изменить</h1>
                <router-link to="/" class="btn btn-secondary">Назад</router-link>
            </div>
        </div>
        <div class="card-body">
            <form action="">
                <div class="form-group">
                    <label for="title">Название;</label>
                    <input type="text" name="title" id="title" class="form-control" v-model="warehouse.name" />
                </div>
                <button type="button" class="btn btn-secondary mt-2" v-on:click="updateWarehouse()">Сохранить</button>
            </form>
        </div>
    </div>
</template>


<script setup>
    import { ref, onMounted } from 'vue';
    import axios from '../config/axios.js';
    import toastr from 'toastr';
    import { useRoute } from 'vue-router';

    const route = useRoute();

    const warehouse = ref({});
    const errors = ref({});

    const updateWarehouse = async () => {
        try {
            const res = await axios.post('/api/warehouses/update', warehouse.value);
            toastr.success('Post updated Successfully');
            await getWarehouse(route.params.id); // Обновляем post после обновления
        } catch (error) {
            errors.value = error.response.data.errors;

            for (const key in errors.value) {
                toastr.error(errors.value[key]);
            }
        }
    };

    const getWarehouse = async (id) => {
        try {
            const res = await axios.get(`/api/warehouses/get/${id}`);
            warehouse.value = res.data.warehouse;
        } catch (error) {
            console.error(error);
        }
    };

    onMounted(() => {
        getWarehouse(route.params.id);
    });

</script>


