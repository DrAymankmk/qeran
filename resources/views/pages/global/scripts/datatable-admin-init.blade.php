<script>
/**
 * Reusable DataTable Initialization Function for Admin Panel
 * This function should be called after including datatable-scripts.blade.php
 *
 * Usage:
 * initAdminDataTable({
 *     tableId: '#categoriesTable',
 *     pdfRoute: '{{route("category.export.pdf")}}', // Optional: for custom PDF export
 *     orderColumn: 0,
 *     orderDirection: 'desc',
 *     nonOrderableColumns: [1, 4],
 *     nonSearchableColumns: [1, 4],
 *     pageLength: 10,
 *     lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, '{{__("admin.all")}}']],
 *     customButtons: [] // Optional: array of custom button objects
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
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, '{{__("admin.all")}}']],
        buttons: ['copy', 'excel', 'pdf', 'print'],
        customButtons: [],
        responsive: true,
        language: null
    };

    // Merge options with defaults
    const config = Object.assign({}, defaults, options);

    // Translations (can be overridden in options)
    const translations = config.translations || {
        copy: '{{__("admin.copy")}}',
        excel: '{{__("admin.excel")}}',
        pdf: '{{__("admin.pdf")}}',
        print: '{{__("admin.print")}}',
        search: '{{__("admin.search")}}',
        show: '{{__("admin.show")}}',
        entries: '{{__("admin.entries")}}',
        showing: '{{__("admin.showing")}}',
        to: '{{__("admin.to")}}',
        of: '{{__("admin.of")}}',
        filtered: '{{__("admin.filtered")}}',
        from: '{{__("admin.from")}}',
        total: '{{__("admin.total")}}',
        first: '{{__("admin.first")}}',
        last: '{{__("admin.last")}}',
        next: '{{__("admin.next")}}',
        previous: '{{__("admin.previous")}}',
        noMatchingRecords: '{{__("admin.no-matching-records")}}',
        noDataAvailable: '{{__("admin.no-data-available")}}',
        all: '{{__("admin.all")}}'
    };

    // Build buttons
    let buttons = [];
    if (config.customButtons.length > 0) {
        buttons = config.customButtons;
    } else {
        if (config.buttons.includes('copy')) {
            buttons.push({
                extend: 'copy',
                className: 'btn btn-sm btn-outline-primary',
                text: '<i class="mdi mdi-content-copy"></i> ' + translations.copy
            });
        }
        if (config.buttons.includes('excel')) {
            buttons.push({
                extend: 'excel',
                className: 'btn btn-sm btn-outline-success',
                text: '<i class="mdi mdi-file-excel"></i> ' + translations.excel
            });
        }
        if (config.buttons.includes('pdf')) {
            if (config.pdfRoute) {
                buttons.push({
                    text: '<i class="mdi mdi-file-pdf"></i> ' + translations.pdf,
                    className: 'btn btn-sm btn-outline-danger',
                    action: function(e, dt, button, btnConfig) {
                        window.location.href = config.pdfRoute;
                    }
                });
            } else {
                buttons.push({
                    extend: 'pdf',
                    className: 'btn btn-sm btn-outline-danger',
                    text: '<i class="mdi mdi-file-pdf"></i> ' + translations.pdf
                });
            }
        }
        if (config.buttons.includes('print')) {
            buttons.push({
                extend: 'print',
                className: 'btn btn-sm btn-outline-info',
                text: '<i class="mdi mdi-printer"></i> ' + translations.print
            });
        }
    }

    // Build language configuration
    let languageConfig = config.language;
    if (!languageConfig) {
        @if(app()->getLocale() == 'ar')
        languageConfig = {
            url: "{{asset('admin_assets/ar.json')}}"
        };
        @else
        languageConfig = {
            search: translations.search + ':',
            lengthMenu: translations.show + ' _MENU_ ' + translations.entries,
            info: translations.showing + ' _START_ ' + translations.to + ' _END_ ' + translations.of + ' _TOTAL_ ' + translations.entries,
            infoEmpty: translations.showing + ' 0 ' + translations.to + ' 0 ' + translations.of + ' 0 ' + translations.entries,
            infoFiltered: '(' + translations.filtered + ' ' + translations.from + ' _MAX_ ' + translations.total + ' ' + translations.entries + ')',
            paginate: {
                first: translations.first,
                last: translations.last,
                next: translations.next,
                previous: translations.previous
            },
            zeroRecords: translations.noMatchingRecords,
            emptyTable: translations.noDataAvailable
        };
        @endif
    }

    // Build columnDefs
    const columnDefs = [];

    // Handle non-orderable columns
    if (config.nonOrderableColumns.length > 0) {
        columnDefs.push({
            targets: config.nonOrderableColumns,
            orderable: false
        });
    }

    // Handle non-searchable columns
    if (config.nonSearchableColumns.length > 0) {
        columnDefs.push({
            targets: config.nonSearchableColumns,
            searchable: false
        });
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
    $('.dataTables_filter input').attr('placeholder', translations.search + '...');

    $('.dataTables_length').addClass('mb-0');
    $('.dataTables_length label').addClass('form-label mb-0 me-2');
    $('.dataTables_length select').addClass('form-select form-select-sm d-inline-block w-auto');

    $('.dt-buttons').addClass('mb-0');

    return table;
}
</script>

