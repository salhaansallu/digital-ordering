<template>
    <div class="col-lg-4">
        <div class="card card-block card-stretch card-height-helf">
            <div class="card-body">
                <div class="d-flex align-items-top justify-content-between">
                    <div class="">
                        <p class="mb-0">Income</p>
                        <h5>{{ currency(totalIncome, "") }}</h5>
                    </div>
                    <div class="">
                        <p class="mb-0 text-end">Expenses</p>
                        <h5>{{ currency(totalExpense, "") }}</h5>
                    </div>
                </div>
                <apexchart height="300" :options="incomeChartOptions" :series="incomeSeries"></apexchart>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card card-block card-stretch card-height">
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                    <h4 class="card-title">Order Summary</h4>
                </div>
                <!-- <div class="card-header-toolbar d-flex align-items-center">
                    <div class="dropdown">
                        <span class="dropdown-toggle dropdown-bg btn" id="dropdownMenuButton005" data-toggle="dropdown">
                            This Month<i class="ri-arrow-down-s-line ml-1"></i>
                        </span>
                        <div class="dropdown-menu dropdown-menu-right shadow-none" aria-labelledby="dropdownMenuButton005">
                            <a class="dropdown-item" href="#">Year</a>
                            <a class="dropdown-item" href="#">Month</a>
                            <a class="dropdown-item" href="#">Week</a>
                        </div>
                    </div>
                </div> -->
            </div>
            <div class="card-body pb-2">
                <div class="d-flex flex-wrap align-items-center mt-2">
                    <div class="d-flex align-items-center progress-order-left">
                        <div class="progress mx-auto primary" :data-value="paidOrderPercent">
                            <span class="progress-left">
                                <span class="progress-bar primary"></span>
                            </span>
                            <span class="progress-right">
                                <span class="progress-bar primary"></span>
                            </span>
                            <div
                                class="progress-value w-100 h-100 rounded-circle d-flex align-items-center justify-content-center">
                                <div class="">{{ paidOrderPercent }}%</div>
                            </div>
                        </div>
                        <div class="progress-value ml-3 pr-5 border-right">
                            <h5>{{ paidOrders }}</h5>
                            <p class="mb-0">Paid Orders</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center ml-5 progress-order-right">
                        <div class="progress mx-auto orange" :data-value="creditOrderPercent">
                            <span class="progress-left">
                                <span class="progress-bar orange"></span>
                            </span>
                            <span class="progress-right">
                                <span class="progress-bar orange"></span>
                            </span>
                            <div
                                class="progress-value w-100 h-100 rounded-circle d-flex align-items-center justify-content-center">
                                <div class="">{{ creditOrderPercent }}%</div>
                            </div>
                        </div>
                        <div class="progress-value ml-3">
                            <h5>{{ creditOrders }}</h5>
                            <p class="mb-0">Credit Orders</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body pt-0">
                <apexchart height="350" :options="summaryChartOptions" :series="summarySeries"></apexchart>
            </div>
        </div>
    </div>
</template>

<script>

import { currency } from "../custom";

export default {
    props: ['dashsales', 'expense', 'summary'],
    data() {
        return {
            name: 'dashboarchart',
            montharr: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            incomeChartOptions: {},
            incomeSeries: [],
            summaryChartOptions: {},
            summarySeries: [],
            totalIncome: 0,
            totalExpense: 0,
            paidOrders: 0,
            creditOrders: 0,
            paidOrderPercent: 0,
            creditOrderPercent: 0,
        }
    },
    methods: {
        currency,
    },
    beforeMount() {

        var temp = this.dashsales;
        var temp3 = [];
        var temp4 = [];
        var expense = this.expense;

        this.montharr.forEach(element => {
            this.totalIncome += temp.hasOwnProperty(element) ? parseFloat(temp[element]) : 0;
            this.totalExpense += expense.hasOwnProperty(element) ? expense[element] : 0;
            temp3.push(temp.hasOwnProperty(element) ? parseFloat(temp[element]).toFixed(2) : 0);
            temp4.push(expense.hasOwnProperty(element) ? parseFloat(expense[element]).toFixed(2) : 0);
        });

        this.incomeChartOptions = {
            colors: ['#32BDEA', '#FF7E41'],
            chart: {
                height: 150,
                type: 'line',
                zoom: {
                    enabled: false
                },
                dropShadow: {
                    enabled: true,
                    color: '#000',
                    top: 12,
                    left: 1,
                    blur: 2,
                    opacity: 0.2
                },
                toolbar: {
                    show: false
                },
                sparkline: {
                    enabled: true,
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            title: {
                text: '',
                align: 'left'
            },
            grid: {
                row: {
                    colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
                    opacity: 0.5
                },
            },
            xaxis: {
                categories: this.montharr,
            }
        };

        this.incomeSeries = [
            {
                name: "Income",
                data: temp3
            },
            {
                name: "Expenses",
                data: temp4
            }
        ];

        var summary = this.summary;
        var paidorders = [];
        var creditorders = [];
        var totalOrders = 0;

        this.montharr.forEach(month => {
            paidorders.push(summary['paid'].hasOwnProperty(month) ? summary['paid'][month] : 0);
            creditorders.push(summary['unpaid'].hasOwnProperty(month) ? summary['unpaid'][month] : 0);

            this.paidOrders += summary['paid'].hasOwnProperty(month) ? summary['paid'][month] : 0;
            this.creditOrders += summary['unpaid'].hasOwnProperty(month) ? summary['unpaid'][month] : 0;

            totalOrders += summary['paid'].hasOwnProperty(month) ? summary['paid'][month] : 0;
            totalOrders += summary['unpaid'].hasOwnProperty(month) ? summary['unpaid'][month] : 0;
        });

        this.paidOrderPercent = ((this.paidOrders / totalOrders) * 100).toFixed(1);
        this.creditOrderPercent = ((this.creditOrders / totalOrders) * 100).toFixed(1);


        this.summarySeries = [{
            name: 'Paid Orders',
            data: paidorders
        }, {
            name: 'Credit Orders',
            data: creditorders
        }];

        this.summaryChartOptions = {
            chart: {
                type: 'bar',
                height: 300
            },
            colors: ['#32BDEA', '#FF7E41'],
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '30%',
                    endingShape: 'rounded'
                },
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 3,
                colors: ['transparent']
            },
            xaxis: {
                categories: this.montharr,
                labels: {
                    minWidth: 0,
                    maxWidth: 0
                }
            },
            yaxis: {
                show: true,
                min: 0,
                labels: {
                    formatter: function (val) {
                        return val.toFixed(0);
                    },
                    minWidth: 20,
                    maxWidth: 20
                },
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val + " Order(s)"
                    }
                }
            }
        };
    },
    mounted() {

    }
}
</script>
