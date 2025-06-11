<template>
    <div class="menu_inner">
        <div class="top_menu d-flex justify-content-between align-items-center">
            <a href="javascript:void(0)" @click="reload()"><i class="fa-solid fa-arrow-rotate-left"></i></a>
            <a href="javascript:void(0)" @click="openCart()"><i class="fa-solid fa-basket-shopping"></i><span class="cart_count">{{ cart.length }}</span></a>
        </div>

        <div class="categories">
            <h5 class="fw-bold text-center mt-4">Our Menu</h5>
            <a v-for="category in categories" href="javascript:void(0)" @click="displayProducts(category.id)" class="category">
                <img :src="'/assets/images/categories/' + category.image" alt="">
                <span>{{ category.category_name }}</span>
            </a>
        </div>

        <div class="products products-opener categories">
            <div class="controls">
                <a href="javascript:void(0)" class="close_product_menu"><i class="fa-solid fa-arrow-left"></i></a>
            </div>

            <p v-if="products.length == 0" class="form-text text-center mt-5">No products found</p>

            <div class="product-wrap" v-for="product in products">
                <a class="category product" data-bs-toggle="collapse" :href="'#product_'+product.sku" role="button" aria-expanded="false" :aria-controls="'product_'+product.sku">
                    <span>{{ product.pro_name }}</span>
                    <span class="text-end">{{ currency(product.price, 'LKR') }}</span>
                </a>

                <div class="collapse variant" :id="'product_'+product.sku">
                    <div class="card card-body p-0 border-0 mt-3">
                        <div v-if="product.has_variant" v-for="variant in product.variants" class="row align-items-center">
                            <hr style="color: #c5c5c5;">
                            <div class="col-12">
                                <div class="row justify-content-between">
                                    <div class="variant-info col-6">{{ variant.pro_name }}</div>
                                    <div class="variant-info col-5 text-end">{{ currency(variant.price, 'LKR') }}</div>
                                </div>
                            </div>
                            <div class="col-6 my-3">
                                <button class="primary-btn px-1 py-1 w-25" @click="changeQTY(variant.sku, '-')" style="border-radius: 0; font-size: 12px;"><i class="fa-solid fa-minus"></i></button>
                                <input type="number" :ref="'product_qty_'+variant.sku" class="w-25 my-1 text-center border-0" style="outline: none;" readonly value="0">
                                <button class="primary-btn px-1 py-1 w-25" @click="changeQTY(variant.sku, '+')" style="border-radius: 0; font-size: 12px;"><i class="fa-solid fa-plus"></i></button>
                            </div>
                        </div>

                        <div v-if="!product.has_variant" class="row align-items-center">
                            <div class="col-6">
                                <button class="primary-btn px-1 py-1 w-25" @click="changeQTY(product.sku, '-')" style="border-radius: 0; font-size: 12px;"><i class="fa-solid fa-minus"></i></button>
                                <input type="number" :ref="'product_qty_'+product.sku" class="w-25 my-1 text-center border-0" style="outline: none;" readonly value="0">
                                <button class="primary-btn px-1 py-1 w-25" @click="changeQTY(product.sku, '+')" style="border-radius: 0; font-size: 12px;"><i class="fa-solid fa-plus"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="carts products categories">
            <div class="controls">
                <a href="javascript:void(0)" class="close_cart_menu"><i class="fa-solid fa-arrow-left"></i></a>
                <h5 class="m-0 fw-bold">Your Cart</h5>
            </div>

            <p v-if="cart.length == 0" class="form-text text-center mt-5">No products in cart</p>

            <div class="product-wrap" v-for="pro in cart">
                <a class="category product" :href="'#product_'+pro.sku">
                    <span>{{ pro.pro_name }}</span>
                    <span class="text-end">{{ currency(pro.price*pro.qty, 'LKR') }}</span>
                </a>
                <div v-if="!pro.has_variant" class="row align-items-center">
                    <div class="col-6">
                        <button class="primary-btn px-1 py-1 w-25" @click="changeQTY(pro.sku, '-')" style="border-radius: 0; font-size: 12px;"><i class="fa-solid fa-minus"></i></button>
                        <input type="number" :ref="'product_qty_'+pro.sku" class="w-25 my-1 text-center border-0" style="outline: none;" readonly :value="pro.qty">
                        <button class="primary-btn px-1 py-1 w-25" @click="changeQTY(pro.sku, '+')" style="border-radius: 0; font-size: 12px;"><i class="fa-solid fa-plus"></i></button>
                    </div>
                </div>
            </div>
        </div>

        <div class="checkout products categories">
            <div class="controls">
                <a href="javascript:void(0)" class="close_checkout_menu"><i class="fa-solid fa-arrow-left"></i></a>
                <h5 class="m-0 fw-bold">Checkout</h5>
            </div>

            <p v-if="cart.length == 0" class="form-text text-center mt-5">Please add products to cart before checkout</p>

            <div class="checkout-wrap p-3" v-if="cart.length > 0">
                <div class="row gap-3">
                    <div class="col-12">
                        <div class="input">
                            <label for="">Customer Name</label>
                            <input type="text" ref="name" placeholder="Enter your name">
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="input">
                            <label for="">Customer Phone Number</label>
                            <input type="number" ref="phone" placeholder="Enter phone number">
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="input">
                            <label for="">Customer Email</label>
                            <input type="email" ref="email" placeholder="Enter email">
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="input">
                            <label for="">Payment Option</label>
                            <select name="" ref="payment" id="">
                                <option value="Cash">Cash</option>
                                <option value="Card">Credit/Debit Card</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="input">
                            <label for="">Order Type</label>
                            <select name="" ref="order_type" id="">
                                <option value="Dine-in">Dine-in</option>
                                <option value="Take Away">Take Away</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-12">
                        <button :class="'mt-4 primary-btn '+(cart.length > 0? '' : 'disabled')" @click="proceed()" style="border-radius: 5px;width: 100%;">Checkout</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="orders products categories">
            <div class="controls">
                <a href="javascript:void(0)" class="close_orders_menu"><i class="fa-solid fa-arrow-left"></i></a>
                <h5 class="m-0 fw-bold">Your Orders</h5>
            </div>

            <p v-if="orders.length == 0" class="form-text text-center mt-5">No orders placed yet</p>

            <div class="product-wrap" v-for="pro in orders">
                <a class="category product">
                    <span>{{ pro.order_number }}</span>
                    <span class="text-end">{{ currency(pro.total, 'LKR') }}</span>
                    <span :class="'text-capitalize text-center badge text-bg-'+(pro.status == 'pending'? 'warning' : (pro.status == 'canceled'? 'danger' : 'success'))">{{ pro.status }}</span>
                </a>
                <hr>
                <div v-if="pro.products.length > 0" class="row align-items-center mt-3">
                    <div class="col-12" v-for="sub_pro in pro.products">
                        <a class="category product">
                            <span class="w-50" style="white-space: nowrap;overflow: hidden;text-overflow: ellipsis;">{{ sub_pro.pro_name }}</span>
                            <span>{{ sub_pro.qty }}x</span>
                            <span class="text-end">{{ currency(sub_pro.price, 'LKR') }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="proceed">
            <div class="inner">
                <div class="total">{{ currency(getCartTotal(), 'LKR') }}</div>
                <button :class="'primary-btn '+(cart.length > 0? '' : 'disabled')" @click="openCheckout()">Proceed</button>
            </div>
        </div>

        <div class="orders_opener" @click="openOrders" v-if="orders.length > 0"><i class="fa-solid fa-mortar-pestle"></i></div>

        <div class="loader" v-if="load">
            <div class="inner">
                <div class="spinner-border text-warning" role="status">
                  <span class="visually-hidden">Loading...</span>
                </div>
                <p>Please wait...</p>
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
    props: ['table_id'],
    data() {
        return {
            orders: [],
            categories: [],
            products: [],
            cart: [],
            proBackup: [],
            load: false,
        }
    },
    methods: {
        currency,
        async getOrders(category) {
            const { data } = await axios.post('/get_orders');
            this.orders = data;

        },
        async getCategories() {
            const { data } = await axios.post('/get_categories');
            this.categories = data
        },
        async getProducts(category) {
            const { data } = await axios.post('/get_products', {
                category: category,
            });
            this.products = data
            this.proBackup = data
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
        async displayProducts(category) {
            this.getProducts(category);
            $('.products-opener').addClass('open');
        },
        openCheckout() {
            $('.checkout').addClass('open');
            $('.proceed').hide();
        },
        async proceed() {
            if (this.cart.length > 0) {
                this.load = true;
                const { data } = await axios.post('/checkout', {
                    products: this.cart,
                    customer_name: this.$refs.name.value,
                    customer_phone: this.$refs.phone.value,
                    customer_email: this.$refs.email.value,
                    table_id: this.table_id,
                    payment_method: this.$refs.payment.value,
                    order_type: this.$refs.order_type.value,
                });

                this.load = false;

                if (data.error == 0) {
                    window.open("/invoice/"+data.url, '_blank');
                    this.getOrders();
                    this.reload();
                }
                else {
                    toastr.error(data.msg,"Error");
                }
            }
            else {
                toastr.error("Please add products to checkout","Error");
            }
        },
        changeQTY(id, operator) {
            switch (operator) {
                case '+':
                    this.$refs['product_qty_'+id][0].value = parseInt(this.$refs['product_qty_'+id][0].value)+1
                    break;
                case '-':
                    if (this.$refs['product_qty_'+id][0].value > 0) {
                        this.$refs['product_qty_'+id][0].value = parseInt(this.$refs['product_qty_'+id][0].value)-1
                    }
                    break;
                default:
                    break;
            }

            this.updateCart(id, parseInt(this.$refs['product_qty_'+id][0].value));
        },
        openDahboard() {
            location.href = "/dashboard";
        },
        updateCart(id, qty) {
            var check = this.cart.filter(item => item['sku'].toLowerCase() == id);
            var pro = this.products.filter(item => item['sku'].toLowerCase() == id);
            var product = [];

            if (qty == 0) {
                this.cart = this.cart.filter(function (obj) {
                    return obj.sku !== id;
                });

                return 0;
            }

            if (pro.length == 0) {
                this.products.forEach(element => {
                    element.variants.forEach(variant => {
                        if (variant['sku'] == id) {
                            product = [
                                {
                                    pro_name: variant.pro_name,
                                    sku: variant.sku,
                                    qty: variant.qty,
                                    price: variant.price,
                                }
                            ]
                        }
                    });
                });
            }
            else {
                product = pro
            }

            if (product.length > 0) {
                if (check.length == 0) {
                    this.cart.push({
                        sku: id,
                        qty: qty,
                        price: product[0]['price'],
                        pro_name: product[0]['pro_name'],
                    });

                }
                else {
                    this.cart.forEach(element => {
                        if (element['sku'] == id) {
                            element['qty'] = qty;
                        }
                    });
                }
            }
        },
        getCartTotal() {
            var total = 0;

            this.cart.forEach(element => {
                total += element['price']*element['qty'];
            });

            return total;
        },
        reload() {
            this.products = [];
            this.cart = [];
            this.$refs.order_type.value = '';
            $('.products').removeClass('open');
            $('.proceed').show();
        },
        openCart() {
            $('.carts').addClass('open');
        },
        openOrders() {
            $('.orders').addClass('open');
        },
        setCookie(name, value, days) {
            let expires = "";
            if (days) {
                let date = new Date();
                date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
                expires = "; expires=" + date.toUTCString();
            }
            document.cookie = name + "=" + encodeURIComponent(value) + expires + "; path=/";
        },
        getCookie(name) {
            let nameEQ = name + "=";
            let cookies = document.cookie.split(";");
            for (let i = 0; i < cookies.length; i++) {
                let cookie = cookies[i].trim();
                if (cookie.indexOf(nameEQ) === 0) {
                    return decodeURIComponent(cookie.substring(nameEQ.length));
                }
            }
            console.log(cookies);

            return null;
        },
        deleteCookie(name) {
            document.cookie = name + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        }
    },
    beforeMount() {
    },
    mounted() {
        this.getCategories();
        this.getOrders();

        setInterval(() => {
            this.getOrders();
        }, 60000);

        $('.close_product_menu').click(function (e) {
            e.preventDefault();
            $('.products-opener').removeClass('open');
            this.products = [];
        });

        $('.close_cart_menu').click(function (e) {
            e.preventDefault();
            $('.carts').removeClass('open');
            this.products = [];
        });

        $('.close_checkout_menu').click(function (e) {
            e.preventDefault();
            $('.checkout').removeClass('open');
            $('.proceed').show();
        });

        $('.close_orders_menu').click(function (e) {
            e.preventDefault();
            $('.orders').removeClass('open');
        });
    }
}
</script>
