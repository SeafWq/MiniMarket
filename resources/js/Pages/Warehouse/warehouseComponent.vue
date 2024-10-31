<template>
    <div class="card">

        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h1>Склады</h1>
                <router-link to="/create-warehouse" class="btn btn-secondary"
                >Создать</router-link
                >
            </div>
        </div>

        <div class="card-body">
            <table class="table">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Название</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(warehouse, key) in warehouses" :key="warehouse.id">
                    <th scope="row">{{key}}</th>
                    <td>{{warehouse.name}}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
    import axios from "../config/axios.js";
    import toastr from "toastr";
    import { ref, onMounted } from 'vue'

    const warehouses = ref([]);
    const getWarehouses = async () => {
        try {
            const res = await axios.get('/api/warehouses');
            warehouses.value = res.data.warehouses;
        } catch (error) {
            console.error(error);
        }
    };


    onMounted(getWarehouses);

</script>

<style scoped>

</style>
