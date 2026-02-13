<style>
    table.dashboard-datatable {
        border-collapse: separate !important;
        border-spacing: 0 10px !important;
    }

    table.dashboard-datatable thead th {
        border-bottom: none !important;
        color: #111111;
        font-size: 14px;
        font-weight: 700;
        padding: 12px 16px !important;
        white-space: nowrap;
    }

    table.dashboard-datatable tbody tr {
        background: #f4f1fb;
    }

    table.dashboard-datatable tbody td {
        border-top: none !important;
        border-bottom: none !important;
        color: #1f1f1f;
        font-size: 16px;
        padding: 14px 16px !important;
        vertical-align: middle;
    }

    table.dashboard-datatable tbody tr td:first-child {
        border-radius: 10px 0 0 10px;
    }

    table.dashboard-datatable tbody tr td:last-child {
        border-radius: 0 10px 10px 0;
    }

    .dashboard-datatable-wrapper .dataTables_length,
    .dashboard-datatable-wrapper .dataTables_filter {
        margin-bottom: 12px;
    }

    .dashboard-datatable-wrapper .dt-head {
        align-items: center;
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        justify-content: space-between;
    }

    .dashboard-datatable-wrapper .dt-head-left {
        align-items: center;
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .dashboard-datatable-wrapper .dt-actions {
        align-items: center;
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-left: auto;
    }

    .dashboard-datatable-wrapper .dt-foot {
        margin-top: 8px;
    }

    .dashboard-datatable-wrapper .dataTables_length label,
    .dashboard-datatable-wrapper .dataTables_filter label {
        align-items: center;
        color: #111111;
        display: inline-flex;
        font-size: 16px;
        font-weight: 500;
        gap: 8px;
    }

    .dashboard-datatable-wrapper .dataTables_length select {
        border: 1px solid #d6d0e2;
        border-radius: 999px;
        color: #1f1f1f;
        padding: 5px 24px 5px 12px;
    }

    .dashboard-datatable-wrapper .dataTables_filter input {
        border: 1px solid #a7a0b5;
        border-radius: 999px;
        margin-left: 8px !important;
        padding: 6px 14px;
        width: 180px;
    }

    .dashboard-datatable-wrapper .dataTables_paginate {
        float: none;
        margin-top: 8px !important;
        text-align: center;
    }

    .dashboard-datatable-wrapper .dataTables_paginate .paginate_button {
        background: #ececec !important;
        border: none !important;
        border-radius: 8px !important;
        color: #5f5f5f !important;
        font-size: 14px;
        margin: 0 4px !important;
        padding: 6px 10px !important;
    }

    .dashboard-datatable-wrapper .dataTables_paginate .paginate_button.current {
        background: #5A2F7E !important;
        color: #ffffff !important;
        width: 40px;
    }

    .dashboard-datatable-wrapper .dataTables_info {
        display: none;
    }
</style>
