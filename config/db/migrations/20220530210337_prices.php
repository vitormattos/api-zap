<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class Prices extends AbstractMigration
{
    public function change(): void
    {
        $this->table('prices')
            ->addColumn('zap_id', 'string')
            ->addColumn('yearly_iptu', 'integer', ['null' => true])
            ->addColumn('price', 'integer')
            ->addColumn('business_type', 'string')
            ->addColumn('monthly_condo_fee', 'integer', ['null' => true])
            ->addColumn('period', 'string', ['null' => true])
            ->addColumn('warranties', 'jsonb', ['null' => true])
            ->addColumn('monthly_rental_total_price', 'integer', ['null' => true])
            ->addForeignKey('zap_id', 'unit', 'zap_id', ['delete'=> 'CASCADE', 'update'=> 'CASCADE'])
            ->create();
    }
}
