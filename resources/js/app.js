import jquery from 'jquery'
window.$ = window.jQuery = jquery

import select2 from 'select2'
select2(jquery)

import 'select2/dist/css/select2.css'

import './bootstrap'
import 'laravel-datatables-vite'

import Alpine from 'alpinejs'
window.Alpine = Alpine
Alpine.start()

function initSelect2() {
    if (typeof $.fn.select2 === 'function') {
        $('.select2').select2({
            width: '100%'
        })
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSelect2)
} else {
    initSelect2()
}
