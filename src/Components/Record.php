<?php

use Minphp\Bridge\Initializer;
use Minphp\Record\Record as MinphpRecord;

/**
 * Record Bridge
 *
 * Intended for legacy backwards compatibility ONLY.
 * Use Minphp\Record\Record instead.
 */
class Record extends MinphpRecord
{
    /**
     * Initialize
     */
    public function __construct(array $dbInfo = null)
    {
        $container = Initializer::get()->getContainer();

        if (null === $dbInfo) {
            $dbInfo = [];
        }

        // Get database info if available
        $config_dbinfo = Configure::get('Database.profile');

        // Check if the connection is communicating with utf8mb4
        $is_mb4 = is_array($config_dbinfo)
            && isset($config_dbinfo['charset_query'])
            && strpos($config_dbinfo['charset_query'], 'utf8mb4');

        // Default new table collation/character set to utf8 if not using utf8mb4
        if (!$is_mb4) {
            $this->setCharacterSet('utf8');
            $this->setCollation('utf8_unicode_ci');
        }

        parent::__construct($dbInfo);

        if (empty($dbInfo)) {
            $this->setConnection($container->get('pdo'));
        }
    }
}
