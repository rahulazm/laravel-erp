<?php

return [
    'bom' => [
        'approval_workflow' => [
            'levels' => 2,
            'final_approver_role' => 'company_admin',
            'auto_version_on_edit' => true,
        ],
        'versioning' => [
            'auto_increment' => true,
            'keep_history' => true,
            'max_versions' => 50,
        ]
    ],
    'item' => [
        'versioning' => [
            'auto_increment' => true,
            'keep_history' => true,
            'max_versions' => 50,
        ],
        'checkout' => [
            'max_hours' => 8,
            'allow_multiple_checkouts' => false,
        ]
    ],
    'reports' => [
        'export_formats' => ['xlsx', 'csv', 'pdf'],
        'default_format' => 'xlsx',
    ],
    'dashboard' => [
        'charts' => [
            'refresh_interval' => 300, // seconds
            'max_data_points' => 12,
        ]
    ]
];