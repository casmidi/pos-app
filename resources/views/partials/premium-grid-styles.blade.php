@once
    @push('css')
        <style>
            .premium-grid-page .content-header h1 {
                font-weight: 700;
                letter-spacing: -0.02em;
                color: #132238;
            }

            .premium-grid-card {
                border: 0;
                border-radius: 18px;
                box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
                overflow: hidden;
                background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            }

            .premium-grid-card .card-header {
                border-bottom: 1px solid rgba(148, 163, 184, 0.18);
                background:
                    radial-gradient(circle at top left, rgba(59, 130, 246, 0.12), transparent 32%),
                    linear-gradient(135deg, #fcfdff 0%, #edf5ff 100%);
                padding: 1.2rem 1.25rem;
            }

            .premium-grid-card .card-footer {
                border-top: 1px solid rgba(148, 163, 184, 0.18);
                background: linear-gradient(180deg, #ffffff 0%, #f7faff 100%);
                padding: 1rem 1.25rem;
            }

            .premium-toolbar label {
                color: #475569;
                font-size: 0.78rem;
                font-weight: 700;
                letter-spacing: 0.06em;
                text-transform: uppercase;
            }

            .premium-toolbar .form-control {
                border-radius: 12px 0 0 12px;
                border: 1px solid rgba(148, 163, 184, 0.3);
                min-height: 46px;
                box-shadow: none;
                background-color: rgba(255, 255, 255, 0.92);
            }

            .premium-toolbar .input-group-append .btn {
                border-radius: 0 12px 12px 0;
                min-height: 46px;
                padding-inline: 1.15rem;
            }

            .premium-toolbar .btn {
                border-radius: 12px;
                font-weight: 700;
                box-shadow: 0 10px 20px rgba(15, 23, 42, 0.08);
            }

            .premium-grid-page .btn-primary {
                background: linear-gradient(135deg, #0f6bff 0%, #1d4ed8 100%);
                border-color: #1d4ed8;
            }

            .premium-grid-page .btn-default {
                background: #fff;
                border-color: rgba(148, 163, 184, 0.35);
                color: #1f2937;
            }

            .premium-grid-table {
                margin-bottom: 0;
            }

            .premium-grid-table thead th {
                background: linear-gradient(180deg, #f9fbff 0%, #edf4ff 100%);
                border-bottom: 1px solid rgba(148, 163, 184, 0.28);
                color: #334155;
                font-size: 0.78rem;
                font-weight: 800;
                letter-spacing: 0.05em;
                text-transform: uppercase;
                white-space: nowrap;
                padding: 1rem 1.1rem;
            }

            .premium-grid-table tbody td {
                border-top: 1px solid rgba(226, 232, 240, 0.85);
                color: #1e293b;
                padding: 1rem 1.1rem;
                vertical-align: middle;
            }

            .premium-grid-table.zebra-grid tbody tr:nth-of-type(odd) {
                background: #f4f6f9;
            }

            .premium-grid-table.zebra-grid tbody tr:nth-of-type(even) {
                background: #ffffff;
            }

            .premium-grid-table tbody tr:hover {
                background: #eaf3ff;
                transition: background-color 0.18s ease;
            }

            .grid-sort-link {
                color: inherit;
                display: inline-flex;
                align-items: center;
                gap: 0.35rem;
            }

            .grid-sort-link:hover {
                color: #0f6bff;
                text-decoration: none;
            }

            .grid-sort-link i {
                font-size: 0.8rem;
                opacity: 0.85;
            }

            .premium-action-cell .btn {
                border-radius: 999px;
                font-weight: 700;
                margin: 0.12rem;
                padding: 0.35rem 0.7rem;
                box-shadow: 0 8px 18px rgba(15, 23, 42, 0.08);
            }

            .premium-action-cell .btn-info {
                background: linear-gradient(135deg, #0891b2 0%, #0ea5e9 100%);
                border-color: transparent;
            }

            .premium-action-cell .btn-warning {
                background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);
                border-color: transparent;
                color: #fff;
            }

            .premium-action-cell .btn-danger {
                background: linear-gradient(135deg, #dc2626 0%, #f43f5e 100%);
                border-color: transparent;
            }

            .premium-status-badge {
                display: inline-flex;
                align-items: center;
                gap: 0.35rem;
                border-radius: 999px;
                font-size: 0.75rem;
                font-weight: 800;
                padding: 0.38rem 0.65rem;
            }

            .premium-grid-card .pagination {
                gap: 0.3rem;
                margin-bottom: 0;
                flex-wrap: wrap;
            }

            .premium-grid-card .page-item .page-link {
                border: 0;
                border-radius: 10px;
                color: #334155;
                font-weight: 700;
                min-width: 42px;
                min-height: 42px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 8px 18px rgba(15, 23, 42, 0.06);
            }

            .premium-grid-card .page-item.active .page-link {
                background: linear-gradient(135deg, #0f6bff 0%, #1d4ed8 100%);
                color: #fff;
            }

            .premium-grid-card .page-item.disabled .page-link {
                background: #e5e7eb;
                color: #94a3b8;
                box-shadow: none;
            }

            .premium-grid-summary {
                color: #64748b;
                font-size: 0.82rem;
                font-weight: 600;
                letter-spacing: 0.02em;
            }

            .premium-grid-page.is-grid-loading {
                opacity: 0.74;
                pointer-events: none;
                transition: opacity 0.2s ease;
                position: relative;
            }

            .premium-grid-page.is-grid-loading::after {
                content: 'Refreshing grid...';
                position: absolute;
                inset: 0;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 0.85rem;
                font-weight: 700;
                letter-spacing: 0.05em;
                text-transform: uppercase;
                color: #0f4fd8;
                background: rgba(248, 250, 252, 0.68);
                backdrop-filter: blur(1px);
            }

            @media (max-width: 767.98px) {
                .premium-grid-card {
                    border-radius: 16px;
                }

                .premium-grid-card .card-header,
                .premium-grid-card .card-footer {
                    padding: 1rem;
                }

                .premium-grid-table thead th,
                .premium-grid-table tbody td {
                    padding: 0.88rem 0.8rem;
                }

                .premium-action-cell {
                    min-width: 180px;
                }
            }
        </style>
    @endpush

@endonce
