import './bootstrap';

import { validateName, checkEmpty, validateCountry, validatePhone, getUrlParam } from './custom';
import { createApp } from 'vue';
import { select2 } from 'select2';

import VueApexCharts from "vue3-apexcharts";

import toastr from 'toastr';
window.toastr = toastr;


toastr.options = {
  timeOut: 4000,
  progressBar: true,
}


window.validateCountry = validateCountry;
window.validateName = validateName;
window.checkEmpty = checkEmpty;
window.validatePhone = validatePhone;

const register_info = createApp({});
import register from './components/register_info.vue';
register_info.component('register-info', register);
register_info.mount('#register_info');

const pos = createApp({});
import poselem from './components/pos.vue';
pos.component('pos', poselem);
pos.mount('#pos-elem');

const verviewChartApp = createApp({});
verviewChartApp.use(VueApexCharts);
import overviewchart from './components/overviewchart.vue';
verviewChartApp.component('dashboard-overview', overviewchart);
verviewChartApp.mount('#overviewChart');

const revenewChartApp = createApp({});
revenewChartApp.use(VueApexCharts);
import revenewChart from './components/revenewChart.vue';
revenewChartApp.component('dashboard-revenew', revenewChart);
revenewChartApp.mount('#revenewChart');

const DashboardCharts = createApp({});
DashboardCharts.use(VueApexCharts);
import dashboarchart from './components/dashboardCharts.vue';
DashboardCharts.component('dashboard-charts', dashboarchart);
DashboardCharts.mount('#dashboard_charts');

const menu = createApp({});
menu.use(VueApexCharts);
import foodMenu from './components/menu.vue';
menu.component('food-menu', foodMenu);
menu.mount('#foodMenu');


$('#menu_close').click(function (e) {
  $('.open_menu').removeClass('hide')
  $('.menulist').removeClass('open')
  $(this).addClass('hide');
});

$('.open_menu').click(function (e) {
  $('.menulist').addClass('open')
  $('#menu_close').removeClass('hide');
  $(this).addClass('hide')
});

$("#passwordForm").submit(function (e) {
  e.preventDefault();

  if ($("#newpass").val() == $("#confirmpass").val()) {
    $.ajax({
      type: "post",
      url: "/update-password",
      data: $("#passwordForm").serialize(),
      dataType: "json",
      success: function (response) {
        if (response.error == 0) {
          toastr.success(response.msg, "Success");
          $("#newpass").val("");
          $("#confirmpass").val("");
          $("#oldpass").val("");
        } else {
          toastr.error(response.msg, "Error");
        }
      }
    });
  }
  else {
    toastr.error("Passwords doesn't match", "Error");
  }
});

$(function () {

  $(".progress").each(function () {

    var value = $(this).attr('data-value');
    var left = $(this).find('.progress-left .progress-bar');
    var right = $(this).find('.progress-right .progress-bar');

    if (value > 0) {
      if (value <= 50) {
        right.css('transform', 'rotate(' + percentageToDegrees(value) + 'deg)')
      } else {
        right.css('transform', 'rotate(180deg)')
        left.css('transform', 'rotate(' + percentageToDegrees(value - 50) + 'deg)')
      }
    }

  })

  function percentageToDegrees(percentage) {

    return percentage / 100 * 360

  }

});


$(".data-table").DataTable();

$('.no-collapsable').on('click', function (e) {
  e.stopPropagation();
});


var keybuffer = [];
var timeHandler = 1;
function press(event) {

  if (event.which === 13) {
    $("#BarCodeValue").val(keybuffer.join(""));
    $('[aria-controls="DataTables_Table_0"]').val(keybuffer.join(""));
    keybuffer.length = 0;
    return true;
  }

  var number = event.which - 48;
  if (number < 0 || number > 9) {
    return;
  }

  if (timeHandler) {
    clearTimeout(timeHandler)
    keybuffer.push(number);
  }

  timeHandler = setTimeout(function () {
    if (keybuffer.length <= 3) {
      keybuffer.length = 0;
      return
    }
  }, 50);
};

$(document).on("keypress", press);


window.checkFileExtension = function (fileID) {
  var fileName = document.querySelector('#'+fileID).value;
  var extension = fileName.split('.').pop();
  return extension;
};


$(document).ready(function() {
    if ($('.select2').length > 0) {
        $('.select2').select2();
    }
});

const SECRET_KEY = '0x4AAAAAAAPWsDLGNsKz84xyJpVqIejpavg';

async function handlePost(request) {
	const body = await request.formData();
	// Turnstile injects a token in "cf-turnstile-response".
	const token = body.get('cf-turnstile-response');
	const ip = request.headers.get('CF-Connecting-IP');

	// Validate the token by calling the
	// "/siteverify" API endpoint.
	let formData = new FormData();
	formData.append('secret', SECRET_KEY);
	formData.append('response', token);
	formData.append('remoteip', ip);

	const url = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';
	const result = await fetch(url, {
		body: formData,
		method: 'POST',
	});

	const outcome = await result.json();
	if (outcome.success) {
		window.recaptcha == true
	}
  else {
    window.recaptcha == false
  }
}
