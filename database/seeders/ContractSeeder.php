<?php

namespace Database\Seeders;

use App\Models\Distributor;
use App\Models\Manufacturer;
use App\Models\Term;
use App\Models\Type;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContractSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types=['Software license','hardware warranty'];
        foreach($types as $type){
            Type::create([
                'name'=> $type
            ]);
        }
        $manufacturers=['HPE','Nutanix','Cisco'];
        foreach($manufacturers as $manufacturer){
            Manufacturer::create([
                'name'=> $manufacturer
            ]);
        }
        $distributors=['TDSynnex','Ingrammicro'];
        foreach($distributors as $distributor){
            Distributor::create([
                'name'=> $distributor
            ]);
        }
        $terms=['1','2','3'];
        foreach($terms as $term){
            Term::create([
                'name'=> $term
            ]);
        }
    }
}
