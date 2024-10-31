<template>
    <div id="app">
            <div class="row flex-nowrap">
                <div class="col-auto col-md-3 col-xl-2 px-sm-2 px-0 bg-dark">
                    <div class="d-flex flex-column align-items-center align-items-sm-start px-3 pt-2 text-white min-vh-100">
                        <a class="d-flex align-items-center pb-3 mb-md-0 me-md-auto text-white text-decoration-none">
                            <span class="fs-5 d-none d-sm-inline">Menu</span>
                        </a>
                        <ul class="nav nav-pills flex-column mb-sm-auto mb-0 align-items-center align-items-sm-start" id="menu">
                            <li class="nav-item">
                                <a class="nav-link align-middle px-0">
                                    <i class="fs-4 bi-house"></i>
                                    <span class="ms-1 d-none d-sm-inline">
                                    <router-link to="/warehouses">Список складов</router-link>
                                    </span>
                                </a>
                            </li>
                            <li>
                                <a class="nav-link px-0 align-middle">
                                    <i class="fs-4 bi-table"></i>
                                    <span class="ms-1 d-none d-sm-inline">
                                   <router-link to="/stocks">Список товаров</router-link>
                                </span>
                                </a>
                            </li>
                            <li>
                                <a class="nav-link px-0 align-middle">
                                    <i class="fs-4 bi-people"></i>
                                    <span class="ms-1 d-none d-sm-inline">
                                    <router-link to="/orders">Список заказов</router-link>
                                    </span>
                                </a>
                            </li>
                            <li>
                                <a class="nav-link px-0 align-middle">
                                    <i class="fs-4 bi-people"></i>
                                    <span class="ms-1 d-none d-sm-inline">
                                    <router-link to="/create-order">Создать заказ</router-link>
                                    </span>
                                </a>
                            </li>
                            <li>
                                <a class="nav-link px-0 align-middle">
                                    <i class="fs-4 bi-people"></i>
                                    <span class="ms-1 d-none d-sm-inline">
                                    <router-link to="/orders-movements">Движения заказов</router-link>
                                    </span>
                                </a>
                            </li>
                        </ul>
                        <hr>
                    </div>
                </div>
                <div class="col-md-10">
                    <RouterView />
                </div>
            </div>
        </div>
</template>

<script>
    import sidebarComponent from './Components/sidebarComponent.vue';
    export default {
        name: "App",
    };
</script>

<style>

</style>
