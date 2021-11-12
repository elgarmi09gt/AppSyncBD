<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ref\EcoleDoctorale;
use App\Models\Scol\EcoleDoctorale as ScolEcoleDoctorale;
use Illuminate\Support\Facades\DB;
use Exception;

class EcoleDoctoraleController extends Controller
{
    public function syncEcoleDoctorale(){
        $ecolesDocs = ScolEcoleDoctorale::all();
        foreach($ecolesDocs as $scol_ecolesDoc){
            $ecolDoc = EcoleDoctorale::where('libelle_long',$scol_ecolesDoc->libelle_long)
            ->where('libelle_court',$scol_ecolesDoc->libelle_court)->first();
            if(!$ecolDoc){
                try {
                    DB::connection('oracle')->table('ecole_doctorale')->insert([
                        'libelle_long' => $scol_ecolesDoc->libelle_long,
                        'libelle_court' => $scol_ecolesDoc->libelle_court,
                        'uti_cree' => 33
                    ]);
                } catch (Exception $e) {
                    dump($e->getMessage());
                }
            }
        }
        session_commit();
        return back()->with('success', 'Synchronisation Ecoles Doctorale effectué');
    }
}
