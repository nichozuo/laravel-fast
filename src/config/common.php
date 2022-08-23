<?php

return [
    'dbTypeToPHPType' => [
        'float' => 'numeric',
        'double' => 'numeric',
        'decimal' => 'numeric',
        'bigint' => 'integer',
        'int' => 'integer',
        'integer' => 'integer',
        'tinyint' => 'integer',
        'smallint' => 'integer',
        'date' => 'date',
        'datetime' => 'date',
        'timestamp' => 'date',
        'boolean' => 'boolean',
        'string' => 'string',
        'text' => 'string',
        'varchar' => 'string',
        'enum' => 'string',
        'array' => 'array',
        'json' => 'json',
        'geometry' => 'geometry',
    ],
    'responseDoNotWrap' => [
//        '/api/app/version/check',
    ],
    'iSeedBackupList' => [
        'admins',
        'personal_access_tokens',
        'sys_permissions',
        'sys_roles',
        'sys_role_has_permissions',
        'sys_model_has_roles',
    ],
    'docs' => [
        'foldersSubTitleConfig' => [
            'Admin' => '管理员模块',
        ]
    ],
    'skipLogPathInfo' => [
        '/api/admin/auth/me'
    ]
];
