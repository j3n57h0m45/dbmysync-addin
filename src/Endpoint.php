<?php

namespace Jensovic\DbMySyncAddin;

class Endpoint
{
    private Auth $auth;
    private array $config;
    private ?SchemaReader $schemaReader = null;

    public function __construct(array $config)
    {
        // Validate config
        $required = ['db_host', 'db_name', 'db_user', 'db_pass', 'secret'];
        foreach ($required as $key) {
            if (!isset($config[$key])) {
                Response::error("Missing required config: {$key}", 500);
            }
        }

        // Initialize components
        $this->auth = new Auth($config['secret']);
        $this->config = $config;
    }

    /**
     * Create the database connection lazily (only after authentication)
     */
    private function getSchemaReader(): SchemaReader
    {
        if ($this->schemaReader === null) {
            $this->schemaReader = new SchemaReader(
                $this->config['db_host'],
                $this->config['db_name'],
                $this->config['db_user'],
                $this->config['db_pass']
            );
        }

        return $this->schemaReader;
    }

    /**
     * Handle incoming request
     */
    public function handle(): void
    {
        // Enable CORS if needed
        $this->setCorsHeaders();

        // Handle OPTIONS request (CORS preflight)
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }

        // Verify authentication
        if (!$this->auth->verify()) {
            $this->auth->sendUnauthorized();
        }

        // Get action from query parameter
        $action = $_GET['action'] ?? 'health';

        try {
            switch ($action) {
                case 'health':
                    $this->handleHealth();
                    break;

                case 'schema':
                    $this->handleSchema();
                    break;

                default:
                    Response::error("Unknown action: {$action}", 404);
            }
        } catch (\Exception $e) {
            error_log('DbMySync: ' . $e->getMessage());
            Response::error('Internal server error', 500);
        }
    }

    /**
     * Health check endpoint
     */
    private function handleHealth(): void
    {
        Response::success([
            'status' => 'ok',
            'version' => '1.0.0',
            'timestamp' => date('c')
        ]);
    }

    /**
     * Schema endpoint
     */
    private function handleSchema(): void
    {
        $table = $_GET['table'] ?? null;

        if ($table) {
            // Get specific table schema
            $schema = $this->getSchemaReader()->getTableSchema($table);
            Response::success($schema);
        } else {
            // Get all tables schema
            $schema = $this->getSchemaReader()->getSchema();
            Response::success([
                'tables' => $schema
            ]);
        }
    }

    /**
     * Set CORS headers for cross-origin requests
     */
    private function setCorsHeaders(): void
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, X-DbSync-Secret, Authorization');
        header('Access-Control-Max-Age: 86400');
    }
}

