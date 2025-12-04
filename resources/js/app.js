/**
 * -------------------------------------------------------
 * Load jQuery FIRST
 * -------------------------------------------------------
 */
import $ from 'jquery';
window.$ = window.jQuery = $;

/**
 * -------------------------------------------------------
 * Load DataTables Core + Extensions (Buttons, Responsive)
 * -------------------------------------------------------
 */
import DataTable from 'datatables.net-bs5';
import 'datatables.net-responsive-bs5';

import 'datatables.net-buttons-bs5';
import 'datatables.net-buttons/js/buttons.html5';
import 'datatables.net-buttons/js/buttons.print';

/**
 * -------------------------------------------------------
 * Export Support (Excel, PDF)
 * -------------------------------------------------------
 */
import JSZip from 'jszip';
window.JSZip = JSZip;

import pdfMake from 'pdfmake/build/pdfmake';
import pdfFonts from 'pdfmake/build/vfs_fonts';
pdfMake.vfs = pdfFonts.pdfMake.vfs;
window.pdfMake = pdfMake;

/**
 * -------------------------------------------------------
 * Make DataTable globally available
 * -------------------------------------------------------
 */
window.DataTable = DataTable;

/**
 * -------------------------------------------------------
 * Bootstrap + Laravel Datatables Vite plugin
 * MUST load AFTER jQuery/DataTables
 * -------------------------------------------------------
 */
import './bootstrap';
import 'laravel-datatables-vite';

/**
 * -------------------------------------------------------
 * Alpine.js
 * -------------------------------------------------------
 */
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();
