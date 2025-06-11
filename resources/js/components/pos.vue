<template>
    <div class="pos-wrap">
        <div class="category">
            <div class="head">
                <h2>Navigations</h2>
            </div>
            <div class="favourits">
                <button @click="openDahboard()" class="primary-btn submit-btn border-only">
                    <i class="fa-solid fa-chart-line"></i>
                    Dashboard
                </button>
            </div>
        </div>
        <div class="products">
            <div class="searchbar">
                <div class="input">
                    <input type="text" ref="searchbar" placeholder="Search here" value=""
                        @keyup="this.searchProducts($event)">
                </div>
            </div>
            <div class="product-wrap">
                <div :class="'product'">
                    <div class="d-flex justify-content-between">
                        <div class="price" style="width: 20%;">Order Number</div>
                        <div class="price" style="width: 20%;">Table Number</div>
                        <div class="price text-center" style="width: 20%;">Customer Name</div>
                        <div class="price text-center" style="width: 20%;">Customer Number</div>
                        <div class="price text-center" style="width: 15%;">Payment Method</div>
                        <div class="price text-center" style="width: 15%;">Order Type</div>
                        <div class="price text-center" style="width: 20%;">Payment Status</div>
                    </div>
                </div>
                <div :class="'product'" v-for="order in orders" data-bs-toggle="collapse" :data-bs-target="'#order_'+order.order_number" aria-expanded="false" :aria-controls="'order_'+order.order_number">
                    <div class="d-flex justify-content-between">
                        <div class="price" style="width: 20%;">{{ order.order_number }}</div>
                        <div class="price" style="width: 20%;">{{ order.order_table }}</div>
                        <div class="price text-center" style="width: 20%;">{{ searchCustomer(order.customer)['name'] }}</div>
                        <div class="price text-center" style="width: 20%;">{{ searchCustomer(order.customer)['phone'] }}</div>
                        <div class="price text-center" style="width: 15%;">{{ order.payment_method }}</div>
                        <div class="price text-center" style="width: 15%;">{{ order.order_type }}</div>
                        <div class="price text-center" style="width: 20%;"><span :class="'badge '+ (order.payment_status == 'paid'? 'text-bg-success' : 'text-bg-warning')">{{ order.payment_status }}</span></div>
                    </div>

                    <div class="collapse mt-4 border-0" :id="'order_'+order.order_number">
                        <div class="card card-body border-0 p-0">
                            <div class="row">
                                <div class="col-12">
                                    <div class="col-lg-12">
                                        <div class="table-responsive rounded mb-3">
                                            <table class="data-table table mb-0 tbl-server-info">
                                                <thead class="bg-white text-uppercase">
                                                    <tr class="ligth ligth-data">
                                                        <th class="text-start">Product</th>
                                                        <th class="text-start">Code (SKU)</th>
                                                        <th class="text-start">QTY</th>
                                                        <th class="text-start">Price</th>
                                                        <th class="text-start">Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="ligth-body">
                                                    <tr id="pro" v-for="product in order.products">
                                                        <td>{{ product.pro_name }}</td>
                                                        <td class="text-start">{{ product.sku }}</td>
                                                        <td class="text-start">{{ product.qty }}</td>
                                                        <td class="text-start">{{ product.price }}</td>
                                                        <td class="text-start">{{ product.price * product.qty }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-3">
                                    <button @click="handleOrder(order.id, 'accepted')" class="btn btn-success">Accept</button>
                                    <button @click="handleOrder(order.id, 'canceled')" class="btn btn-danger mx-2">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="loadingModal" tabindex="-1" role="dialog" aria-labelledby="loadingModalTitle"
        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content bg-transparent border-0">
                <div class="modal-body d-flex" style="justify-content: center;">
                    <div class="spinner-border text-primary" style="width: 4rem; height: 4rem;" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import { ref } from 'vue';
import toastr from 'toastr';
import { validateName, checkEmpty, validateCountry, validatePhone, getUrlParam, currency } from '../custom';
import axios from 'axios';

export default {
    props: ['app_url'],
    data() {
        return {
            orders: [],
        }
    },
    methods: {
        currency,
        async getOrders(){
            const { data } = await axios.post('/pos/get_orders');
            this.orders = data
        },
        searchCustomer(data) {
            try {
                data = JSON.parse(data);
                return data;
            } catch (error) {
                return { "name": "N/A", "phone": "N/A", "email": "N/A" }
            }

            return { "name": "N/A", "phone": "N/A", "email": "N/A" }
        },
        async handleOrder(id, status) {
            const { data } = await axios.post("/pos/update_orders", {
                id: id,
                status: status
            });
            if (data.error == 0) {
                toastr.success(data.msg, 'Success');
                this.getOrders();
            }
            else {
                toastr.error(data.msg, 'Error');
            }
        },
        openDahboard() {
            location.href="/dashboard";
        }
    },
    beforeMount() {
    },
    mounted() {
        this.getOrders();
        setInterval(() => {
            this.getOrders();
            console.log(this.orders);
        }, 30000);
    }
}
</script>
