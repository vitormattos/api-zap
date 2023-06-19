<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class Address extends AbstractMigration
{
    public function change(): void
    {
        $this->table('address')
            ->addColumn('zap_id', 'string')
            ->addColumn('city', 'string', ['null' => true])
            ->addColumn('name', 'string', ['null' => true])
            ->addColumn('pois', 'string', ['null' => true])
            ->addColumn('zone', 'string', ['null' => true])
            ->addColumn('level', 'string', ['null' => true])
            ->addColumn('state', 'string', ['null' => true])
            ->addColumn('source', 'string', ['null' => true])
            ->addColumn('street', 'string', ['null' => true])
            ->addColumn('country', 'string', ['null' => true])
            ->addColumn('neighborhood', 'string', ['null' => true])
            ->addColumn('state_acronym', 'string', ['null' => true])
            ->addColumn('complement', 'string', ['null' => true])
            ->addColumn('precision', 'string', ['null' => true])
            ->addColumn('zip_code', 'string', ['null' => true])
            ->addColumn('lat', 'string', ['null' => true])
            ->addColumn('lon', 'string', ['null' => true])
            ->addForeignKey('zap_id', 'unit', 'zap_id', ['delete'=> 'CASCADE', 'update'=> 'CASCADE'])
            ->create();
    }
}
