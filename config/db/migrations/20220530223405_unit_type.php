<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class UnitType extends AbstractMigration
{
    public function change(): void
    {
        $this->table('unit_type')
            ->addColumn('zap_id', 'string')
            ->addColumn('type', 'string')
            ->addForeignKey('zap_id', 'unit', 'zap_id', ['delete'=> 'CASCADE', 'update'=> 'CASCADE'])
            ->create();
    }
}
