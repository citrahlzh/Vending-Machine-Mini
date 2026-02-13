<script>
    window.initDashboardDataTable = function(options = {}) {
        if (!window.jQuery || !jQuery.fn.DataTable || !options.selector) return null;

        const selector = options.selector;
        const $table = jQuery(selector);
        if (!$table.length) return null;

        $table.addClass('dashboard-datatable');

        const table = $table.DataTable({
            pagingType: options.pagingType || 'simple_numbers',
            pageLength: options.pageLength || 10,
            lengthMenu: options.lengthMenu || [10, 25, 50],
            language: {
                lengthMenu: 'Lihat _MENU_ baris',
                search: '',
                searchPlaceholder: options.searchPlaceholder || 'Cari...',
                emptyTable: 'Tidak ada data di tabel',
                zeroRecords: 'Data tidak ditemukan',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                infoEmpty: 'Menampilkan 0 sampai 0 dari 0 data',
                infoFiltered: '(difilter dari _MAX_ total data)',
                paginate: {
                    previous: 'Sebelumnya',
                    next: 'Selanjutnya'
                }
            },
            dom: options.dom || '<"dt-head"<"dt-head-left"lf><"dt-actions">>rt<"dt-foot"p>',
            order: options.order || [],
        });

        const $wrapper = jQuery(table.table().container());
        $wrapper.addClass('dashboard-datatable-wrapper');

        if (options.actionContainerSelector) {
            const $container = jQuery(options.actionContainerSelector);
            if ($container.length) {
                const $actionElements = $container.find('button, a, [data-dt-action]');
                if ($actionElements.length) {
                    $actionElements.each(function() {
                        jQuery(this).appendTo($wrapper.find('.dt-actions'));
                    });
                    $container.remove();
                }
            }
        }

        $wrapper.find('.dataTables_filter input').attr('placeholder', options.searchPlaceholder || 'Cari...');
        return table;
    };
</script>
