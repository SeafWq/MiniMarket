import CreateWarehouse from "./Pages/Warehouse/createWarehouse.vue";
import EditWarehouse from "./Pages/Warehouse/editWarehouse.vue";
import WarehouseComponent from "./Pages/Warehouse/warehouseComponent.vue";
import StockComponent from "./Pages/Stock/stockComponent.vue";
import OrderComponent from "./Pages/Order/orderComponent.vue";
import CreateOrder from "./Pages/Order/createOrderComponent.vue";
import EditOrderComponent from "./Pages/Order/editOrderComponent.vue";
import StocksMovementsComponent from "./Pages/Stock/stocksMovementsComponent.vue";

export const routes = [
    {
        path: "/create-warehouse",
        name: "CreateWarehouse",
        component: CreateWarehouse,
    },
    {
        path: "/edit-warehouse/:id",
        name: "EditWarehouse",
        component: EditWarehouse,
    },
    {
        path: "/warehouses",
        name: "warehouseComponent",
        component: WarehouseComponent,
    },
    {
        path: "/stocks",
        name: "StockComponent",
        component: StockComponent,
    },
    {
        path: "/create-order",
        name: "CreateOrder",
        component: CreateOrder,
    },
    {
        path: "/orders",
        name: "OrderComponent",
        component: OrderComponent,
    },
    {
        path: "/edit-order/:id",
        name: "EditOrderComponent",
        component: EditOrderComponent,
    },{
        path: "/orders-movements",
        name: "StocksMovementsComponent",
        component: StocksMovementsComponent,
    },

];
