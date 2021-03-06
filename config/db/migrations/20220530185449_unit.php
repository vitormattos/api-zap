<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class Unit extends AbstractMigration
{
    public function change(): void
    {
        $this->table('unit', ['id' => false, 'primary_key' => ['zap_id']])
            ->addColumn('data', 'jsonb')
            ->addColumn('zap_id', 'string')
            ->addColumn('title', 'string')
            ->addColumn('bedrooms', 'integer', ['null' => true])
            ->addColumn('bathrooms', 'integer', ['null' => true])
            ->addColumn('total_areas', 'integer', ['null' => true])
            ->create();
    }
}
