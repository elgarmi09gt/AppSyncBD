<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ref\Domaine;
use App\Models\Scol\Domaine as ScolDomaine;
use Illuminate\Support\Facades\DB;
use Exception;

class DomaineController extends Controller
{
    public function synchDomaine(){
        $scol_domaines = ScolDomaine::all();
        foreach($scol_domaines as $scol_domaine){
            $domaine = Domaine::where('libelle_long',$scol_domaine->lib_long_domaine)
            ->where('libelle_court',$scol_domaine->lib_court_domaine)
            ->first();
            if(!$domaine){
                try {
                    DB::connection('oracle')->table('domaines')->insert([
                        'libelle_long' => $scol_domaine->lib_long_domaine,
                        'libelle_court' => $scol_domaine->lib_court_domaine,
                        'uti_cree' => 33
                    ]);
                } catch (Exception $e) {
                    dump($e->getMessage());
                }
            }
        }
        session_commit();
        return back()->with('success', 'Synchronisation Domaines effectué');
    }
}
