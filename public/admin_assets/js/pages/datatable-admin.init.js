/**
 * Reusable DataTable Initialization Function for Admin Panel
 *
 * Usage:
 * initAdminDataTable({
 *     tableId: '#categoriesTable',
 *     pdfRoute: 'category.export.pdf', // Optional: for custom PDF export
 *     orderColumn: 0, // Column index for default ordering
 *     orderDirection: 'desc', // 'asc' or 'desc'
 *     nonOrderableColumns: [1, 4], // Column indices that should not be orderable
 *     nonSearchableColumns: [1, 4], // Column indices that should not be searchable
 *     pageLength: 10,
 *     lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
 *     buttons: ['copy', 'excel', 'pdf', 'print'], // or custom button config
 *     customButtons: [] // Array of custom button objects
 * });
 */
function initAdminDataTable(options) {
    // Default options
    const defaults = {
        tableId: '#dataTable',
        pdfRoute: null,
        orderColumn: 0,
        orderDirection: 'desc',
        nonOrderableColumns: [],
        nonSearchableColumns: [],
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
        buttons: ['copy', 'excel', 'pdf', 'print'],
        customButtons: [],
        responsive: true,
        language: null // Will use default or locale-based language
    };

    // Merge options with defaults
    const config = Object.assign({}, defaults, options);

    // Build default buttons if not custom
    let buttons = [];
    if (config.customButtons.length > 0) {
        buttons = config.customButtons;
    } else {
        // Standard buttons
        if (config.buttons.includes('copy')) {
            buttons.push({
                extend: 'copy',
                className: 'btn btn-sm btn-outline-primary',
                text: '<i class="mdi mdi-content-copy"></i> ' + (window.adminTranslations?.copy || 'Copy')
            });
        }
        if (config.buttons.includes('excel')) {
            buttons.push({
                extend: 'excel',
                className: 'btn btn-sm btn-outline-success',
                text: '<i class="mdi mdi-file-excel"></i> ' + (window.adminTranslations?.excel || 'Excel')
            });
        }
        if (config.buttons.includes('pdf')) {
            if (config.pdfRoute) {
                // Custom PDF export route
                buttons.push({
                    text: '<i class="mdi mdi-file-pdf"></i> ' + (window.adminTranslations?.pdf || 'PDF'),
                    className: 'btn btn-sm btn-outline-danger',
                    action: function(e, dt, button, config) {
                        window.location.href = window.adminRoutes?.[config.pdfRoute] || '#';
                    }
                });
            } else {
                // Default PDF button
                buttons.push({
                    extend: 'pdf',
                    className: 'btn btn-sm btn-outline-danger',
                    text: '<i class="mdi mdi-file-pdf"></i> ' + (window.adminTranslations?.pdf || 'PDF')
                });
            }
        }
        if (config.buttons.includes('print')) {
            buttons.push({
                extend: 'print',
                className: 'btn btn-sm btn-outline-info',
                text: '<i class="mdi mdi-printer"></i> ' + (window.adminTranslations?.print || 'Print')
            });
        }
    }

    // Build language configuration
    let languageConfig = config.language;
    if (!languageConfig) {
        const locale = window.appLocale || 'en';
        if (locale === 'ar') {
            languageConfig = {
                url: window.adminAssetsPath + '/ar.json'
            };
        } else {
            languageConfig = {
                search: (window.adminTranslations?.search || 'Search') + ':',
                lengthMenu: (window.adminTranslations?.show || 'Show') + ' _MENU_ ' + (window.adminTranslations?.entries || 'entries'),
                info: (window.adminTranslations?.showing || 'Showing') + ' _START_ ' + (window.adminTranslations?.to || 'to') + ' _END_ ' + (window.adminTranslations?.of || 'of') + ' _TOTAL_ ' + (window.adminTranslations?.entries || 'entries'),
                infoEmpty: (window.adminTranslations?.showing || 'Showing') + ' 0 ' + (window.adminTranslations?.to || 'to') + ' 0 ' + (window.adminTranslations?.of || 'of') + ' 0 ' + (window.adminTranslations?.entries || 'entries'),
                infoFiltered: '(' + (window.adminTranslations?.filtered || 'filtered') + ' ' + (window.adminTranslations?.from || 'from') + ' _MAX_ ' + (window.adminTranslations?.total || 'total') + ' ' + (window.adminTranslations?.entries || 'entries') + ')',
                paginate: {
                    first: window.adminTranslations?.first || 'First',
                    last: window.adminTranslations?.last || 'Last',
                    next: window.adminTranslations?.next || 'Next',
                    previous: window.adminTranslations?.previous || 'Previous'
                },
                zeroRecords: window.adminTranslations?.noMatchingRecords || 'No matching records found',
                emptyTable: window.adminTranslations?.noDataAvailable || 'No data available'
            };
        }
    }

    // Build columnDefs
    const columnDefs = [];
    if (config.nonOrderableColumns.length > 0 || config.nonSearchableColumns.length > 0) {
        const targets = [...new Set([...config.nonOrderableColumns, ...config.nonSearchableColumns])];
        const def = { targets: targets };
        if (config.nonOrderableColumns.length > 0) {
            def.orderable = false;
        }
        if (config.nonSearchableColumns.length > 0) {
            def.searchable = false;
        }
        columnDefs.push(def);
    }

    // Initialize DataTable
    const table = $(config.tableId).DataTable({
        dom: '<"row mb-3 align-items-center justify-content-between"<"col-auto"l><"col-auto ms-3"f><"col-auto ms-3"B>>' +
            'rt' +
            '<"row"<"col-md-5"i><"col-md-7"p>>',
        buttons: buttons,
        responsive: config.responsive,
        pageLength: config.pageLength,
        lengthMenu: config.lengthMenu,
        order: [[config.orderColumn, config.orderDirection]],
        language: languageConfig,
        columnDefs: columnDefs.length > 0 ? columnDefs : undefined,
        drawCallback: function() {
            $('.dataTables_length select').addClass('form-select form-select-sm');
        }
    });

    // Style the elements
    $('.dataTables_filter').addClass('mb-0');
    $('.dataTables_filter input').addClass('form-control form-control-sm');
    $('.dataTables_filter input').attr('placeholder', (window.adminTranslations?.search || 'Search') + '...');

    $('.dataTables_length').addClass('mb-0');
    $('.dataTables_length label').addClass('form-label mb-0 me-2');
    $('.dataTables_length select').addClass('form-select form-select-sm d-inline-block w-auto');

    $('.dt-buttons').addClass('mb-0');

    return table;
}

