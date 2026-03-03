<?php

declare(strict_types=1);

return [
    'doctrine' => [
        'migrations_configuration' => [
            'orm_default' => [
                'table_storage' => [
                    'table_name' => 'DoctrineMigrationVersions',
                    'version_column_name' => 'version',
                    'version_column_length' => 191,
                    'executed_at_column_name' => 'executedAt',
                    'execution_time_column_name' => 'executionTime',
                ],

                // namespace => path
                'migrations_paths' => [
                    'Application\\Migrations' => 'data/migrations',
                ],

                // Boas práticas:
                'all_or_nothing' => true,
                'check_database_platform' => true,

                // organiza por ano e mês
                'organize_migrations' => 'year_and_month',

                'custom_template' => null,
            ],
        ],
    ],
];