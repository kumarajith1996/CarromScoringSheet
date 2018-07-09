<?php
use Migrations\AbstractMigration;

class Initial extends AbstractMigration
{
    public function up()
    {

        $this->table('boards')
            ->addColumn('queen', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('finisher', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('match_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addIndex(
                [
                    'finisher',
                ]
            )
            ->addIndex(
                [
                    'match_id',
                ]
            )
            ->addIndex(
                [
                    'queen',
                ]
            )
            ->create();

        $this->table('boards_players')
            ->addColumn('board_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('player_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('coins', 'integer', [
                'default' => '0',
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('opc', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('minus', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addIndex(
                [
                    'board_id',
                ]
            )
            ->addIndex(
                [
                    'player_id',
                ]
            )
            ->create();

        $this->table('matches')
            ->addColumn('team1_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('team2_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('role', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('winner', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addIndex(
                [
                    'team1_id',
                ]
            )
            ->addIndex(
                [
                    'team2_id',
                ]
            )
            ->addIndex(
                [
                    'winner',
                ]
            )
            ->create();

        $this->table('players')
            ->addColumn('name', 'string', [
                'default' => null,
                'limit' => 100,
                'null' => false,
            ])
            ->addColumn('image_url', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('role_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('base_points', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('team_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('bid_value', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addIndex(
                [
                    'role_id',
                ]
            )
            ->addIndex(
                [
                    'team_id',
                ]
            )
            ->addIndex(
                [
                    'team_id',
                ]
            )
            ->create();

        $this->table('roles')
            ->addColumn('name', 'string', [
                'default' => null,
                'limit' => 50,
                'null' => false,
            ])
            ->create();

        $this->table('teams')
            ->addColumn('name', 'string', [
                'default' => null,
                'limit' => 100,
                'null' => false,
            ])
            ->addColumn('bid_points', 'integer', [
                'default' => '100',
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('played', 'integer', [
                'default' => '0',
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('won', 'integer', [
                'default' => '0',
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('points', 'integer', [
                'default' => '0',
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('loss', 'integer', [
                'default' => '0',
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('_group', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->create();

        $this->table('boards')
            ->addForeignKey(
                'finisher',
                'players',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->addForeignKey(
                'match_id',
                'matches',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->addForeignKey(
                'queen',
                'players',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->update();

        $this->table('boards_players')
            ->addForeignKey(
                'board_id',
                'boards',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->addForeignKey(
                'player_id',
                'players',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->update();

        $this->table('matches')
            ->addForeignKey(
                'team1_id',
                'teams',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->addForeignKey(
                'team2_id',
                'teams',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->addForeignKey(
                'winner',
                'teams',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->update();

        $this->table('players')
            ->addForeignKey(
                'role_id',
                'roles',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->addForeignKey(
                'team_id',
                'teams',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->addForeignKey(
                'team_id',
                'teams',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->update();
    }

    public function down()
    {
        $this->table('boards')
            ->dropForeignKey(
                'finisher'
            )
            ->dropForeignKey(
                'match_id'
            )
            ->dropForeignKey(
                'queen'
            );

        $this->table('boards_players')
            ->dropForeignKey(
                'board_id'
            )
            ->dropForeignKey(
                'player_id'
            );

        $this->table('matches')
            ->dropForeignKey(
                'team1_id'
            )
            ->dropForeignKey(
                'team2_id'
            )
            ->dropForeignKey(
                'winner'
            );

        $this->table('players')
            ->dropForeignKey(
                'role_id'
            )
            ->dropForeignKey(
                'team_id'
            )
            ->dropForeignKey(
                'team_id'
            );

        $this->dropTable('boards');
        $this->dropTable('boards_players');
        $this->dropTable('matches');
        $this->dropTable('players');
        $this->dropTable('roles');
        $this->dropTable('teams');
    }
}
