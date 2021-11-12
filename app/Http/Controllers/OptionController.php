<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Exception;
use App\Models\Ref\Specialite;
use App\Models\Scol\Specialite as ScolSpecialite;
use App\Models\Ref\Option;
use App\Models\Scol\Option as ScolOption;

class OptionController extends Controller
{
    public function syncOption(){
        $scolOptions = ScolOption::all();
        foreach($scolOptions as $scolOption){
            $scolSpecialite = ScolSpecialite::where('code_specialite_diplome',$scolOption->id_specialite)->first();
            if($scolSpecialite){
                $specialite = Specialite::where('libelle_long',$scolSpecialite->lib_specialite)
                ->where('libelle_court',$scolSpecialite->lib_court_specialite)
                ->first();
                if($specialite){
                    $option = Option::where('libelle_long',$scolOption->libelle_long)
                    ->where('libelle_court',$scolOption->libelle_court)
                    ->where('id_specialite',$specialite->id_specialite)
                    ->first();
                    if(!$option){
                        try {
                            DB::connection('oracle')->table('options')->insert([
                                'libelle_long' => $scolOption->libelle_long,
                                'libelle_court' => $scolOption->libelle_court,
                                'id_specialite' => $specialite->id_specialite,
                                'uti_cree' => 33
                            ]);
                        } catch (Exception $e) {
                            dump($e->getMessage());
                        }
                    }
                }
            }
        }
        session_commit();
        return back()->with('success', 'Synchronisation Options effectué');
    }
}
