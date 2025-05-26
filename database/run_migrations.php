<?php
// Include database configuration
require_once __DIR__ . '/../config/database.php';

// Include the database connection file which defines $conn
global $conn;

class MigrationRunner {
    private $conn;
    private $migrationsDir;
    private $migrationsTable = 'migrations';

    public function __construct($connection) {
        $this->conn = $connection;
        $this->migrationsDir = __DIR__ . '/migrations';
        $this->ensureMigrationsTableExists();
    }

    private function ensureMigrationsTableExists() {
        $sql = "CREATE TABLE IF NOT EXISTS `{$this->migrationsTable}` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `migration` VARCHAR(255) NOT NULL,
            `applied_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY `unique_migration` (`migration`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        
        $this->conn->query($sql);
    }

    public function run() {
        echo "Starting migrations...\n";
        
        // Get all migration files
        $migrationFiles = glob($this->migrationsDir . '/*.sql');
        sort($migrationFiles);
        
        $appliedMigrations = $this->getAppliedMigrations();
        $newMigrations = [];
        
        foreach ($migrationFiles as $file) {
            $migrationName = basename($file);
            
            // Skip if already applied
            if (in_array($migrationName, $appliedMigrations)) {
                continue;
            }
            
            echo "Applying migration: $migrationName\n";
            
            // Read and execute the migration
            $sql = file_get_contents($file);
            
            try {
                $this->conn->multi_query($sql);
                
                // Clear any remaining results
                while ($this->conn->more_results() && $this->conn->next_result()) {
                    if ($result = $this->conn->store_result()) {
                        $result->free();
                    }
                }
                
                if ($this->conn->error) {
                    throw new Exception("Migration failed: " . $this->conn->error);
                }
                
                // Record the migration
                $newMigrations[] = $migrationName;
                $this->saveMigration($migrationName);
                
                echo "Applied: $migrationName\n";
            } catch (Exception $e) {
                die("Error applying migration $migrationName: " . $e->getMessage() . "\n");
            }
        }
        
        if (empty($newMigrations)) {
            echo "No new migrations to apply.\n";
        } else {
            echo "\nSuccessfully applied " . count($newMigrations) . " migration(s).\n";
        }
    }
    
    private function getAppliedMigrations() {
        $result = $this->conn->query("SELECT migration FROM `{$this->migrationsTable}`");
        return $result ? array_column($result->fetch_all(MYSQLI_ASSOC), 'migration') : [];
    }
    
    private function saveMigration($migrationName) {
        $stmt = $this->conn->prepare("INSERT INTO `{$this->migrationsTable}` (migration) VALUES (?)");
        $stmt->bind_param('s', $migrationName);
        $stmt->execute();
        $stmt->close();
    }
}

// Run migrations
$migration = new MigrationRunner($conn);
$migration->run();

echo "Migrations completed.\n";
?>
