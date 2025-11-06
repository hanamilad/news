<?php

declare(strict_types=1);


return [
    'route' => [
        'uri' => '/graphql-sb',
        'name' => 'graphql.sb',
        'middleware' => [
            'api',
            \Stancl\Tenancy\Middleware\InitializeTenancyByDomain::class,
            \Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains::class,
        ],
    ],
    'guards' => ['sanctum'],
    'schema_path' => base_path('graphql/schema.graphql'),
    'schema_cache' => [
        'enable' => env('LIGHTHOUSE_SCHEMA_CACHE_ENABLE', env('APP_ENV') !== 'local'),
        'path' => env('LIGHTHOUSE_SCHEMA_CACHE_PATH', base_path('bootstrap/cache/lighthouse-schema.php')),
    ],
    'cache_directive_tags' => false,
    'query_cache' => [
        'enable' => env('LIGHTHOUSE_QUERY_CACHE_ENABLE', true),
        'mode' => env('LIGHTHOUSE_QUERY_CACHE_MODE', 'store'),
        'opcache_path' => env('LIGHTHOUSE_QUERY_CACHE_OPCACHE_PATH', base_path('bootstrap/cache')),
        'store' => env('LIGHTHOUSE_QUERY_CACHE_STORE', null),
        'ttl' => env('LIGHTHOUSE_QUERY_CACHE_TTL', 24 * 60 * 60),
    ],
    'validation_cache' => [
        'enable' => env('LIGHTHOUSE_VALIDATION_CACHE_ENABLE', false),
        'store' => env('LIGHTHOUSE_VALIDATION_CACHE_STORE', null),
        'ttl' => env('LIGHTHOUSE_VALIDATION_CACHE_TTL', 24 * 60 * 60),
    ],
    'parse_source_location' => true,
    'namespaces' => [
        'models' => ['App', 'App\\Models'],
        'queries' => 'App\\GraphQL\\Queries',
        'mutations' => 'App\\GraphQL\\Mutations',
        'subscriptions' => 'App\\GraphQL\\Subscriptions',
        'types' => 'App\\GraphQL\\Types',
        'interfaces' => 'App\\GraphQL\\Interfaces',
        'unions' => 'App\\GraphQL\\Unions',
        'scalars' => 'App\\GraphQL\\Scalars',
        'directives' => 'App\\GraphQL\\Directives',
        'validators' => 'App\\GraphQL\\Validators',
    ],
    'security' => [
        'max_query_complexity' => GraphQL\Validator\Rules\QueryComplexity::DISABLED,
        'max_query_depth' => GraphQL\Validator\Rules\QueryDepth::DISABLED,
        'disable_introspection' => (bool) env('LIGHTHOUSE_SECURITY_DISABLE_INTROSPECTION', false)
            ? GraphQL\Validator\Rules\DisableIntrospection::ENABLED
            : GraphQL\Validator\Rules\DisableIntrospection::DISABLED,
    ],
    'pagination' => [
        'default_count' => null,
        'max_count' => null,
    ],
    'debug' => env('LIGHTHOUSE_DEBUG', GraphQL\Error\DebugFlag::INCLUDE_DEBUG_MESSAGE | GraphQL\Error\DebugFlag::INCLUDE_TRACE),
    'error_handlers' => [
        Nuwave\Lighthouse\Execution\AuthenticationErrorHandler::class,
        Nuwave\Lighthouse\Execution\AuthorizationErrorHandler::class,
        Nuwave\Lighthouse\Execution\ValidationErrorHandler::class,
        Nuwave\Lighthouse\Execution\ReportingErrorHandler::class,
    ],
    'field_middleware' => [
        Nuwave\Lighthouse\Schema\Directives\TrimDirective::class,
        Nuwave\Lighthouse\Schema\Directives\ConvertEmptyStringsToNullDirective::class,
        Nuwave\Lighthouse\Schema\Directives\SanitizeDirective::class,
        Nuwave\Lighthouse\Validation\ValidateDirective::class,
        Nuwave\Lighthouse\Schema\Directives\TransformArgsDirective::class,
        Nuwave\Lighthouse\Schema\Directives\SpreadDirective::class,
        Nuwave\Lighthouse\Schema\Directives\RenameArgsDirective::class,
        Nuwave\Lighthouse\Schema\Directives\DropArgsDirective::class,
    ],
    'global_id_field' => 'id',
    'persisted_queries' => true,
    'transactional_mutations' => true,
    'force_fill' => true,
    'batchload_relations' => true,
    'shortcut_foreign_key_selection' => false,
    'subscriptions' => [
        'queue_broadcasts' => env('LIGHTHOUSE_QUEUE_BROADCASTS', true),
        'broadcasts_queue_name' => env('LIGHTHOUSE_BROADCASTS_QUEUE_NAME', null),
        'storage' => env('LIGHTHOUSE_SUBSCRIPTION_STORAGE', 'redis'),
        'storage_ttl' => env('LIGHTHOUSE_SUBSCRIPTION_STORAGE_TTL', null),
        'encrypted_channels' => env('LIGHTHOUSE_SUBSCRIPTION_ENCRYPTED', false),
        'broadcaster' => env('LIGHTHOUSE_BROADCASTER', 'pusher'),
        'broadcasters' => [
            'log' => [
                'driver' => 'log',
            ],
            'echo' => [
                'driver' => 'echo',
                'connection' => env('LIGHTHOUSE_SUBSCRIPTION_REDIS_CONNECTION', 'default'),
                'routes' => Nuwave\Lighthouse\Subscriptions\SubscriptionRouter::class . '@echoRoutes',
            ],
            'pusher' => [
                'driver' => 'pusher',
                'connection' => 'pusher',
                'routes' => Nuwave\Lighthouse\Subscriptions\SubscriptionRouter::class . '@pusher',
            ],
            'reverb' => [
                'driver' => 'pusher',
                'connection' => 'reverb',
                'routes' => Nuwave\Lighthouse\Subscriptions\SubscriptionRouter::class . '@reverb',
            ],
        ],
        'exclude_empty' => env('LIGHTHOUSE_SUBSCRIPTION_EXCLUDE_EMPTY', true),
    ],
    'defer' => [
        'max_nested_fields' => 0,
        'max_execution_ms' => 0,
    ],
    'federation' => [
        'entities_resolver_namespace' => 'App\\GraphQL\\Entities',
    ],
    'playground' => [
        'enabled' => true,
        'route' => '/graphql-playground',
        'middleware' => ['web'],
    ],
    'tracing' => [
        'driver' => Nuwave\Lighthouse\Tracing\ApolloTracing\ApolloTracing::class,
    ],
    'scalars' => [
        'JSON' => App\GraphQL\Scalars\JSON::class,
    ],

];
