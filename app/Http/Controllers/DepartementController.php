<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ref\Departement;
use App\Models\Scol\Departement as Section;
use App\Models\Ref\Etablissement as Structure;
use App\Models\Scol\Etablissement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;

class DepartementController extends Controller
{
    public function synchDepartement()
    {
        //Département dans Scolarite
        $sections = Section::where('type_section','!=',null)->get();
        foreach ($sections as $section) {
            if(str_starts_with($section->libelle_long, '..')){
                $section->libelle_long = "Tron Commun";
                $section->libelle_court = "Tron Commun";
            }
            //Etablissement dans Scolarité
            $etab = Etablissement::where('code', $section->etablissem_code)->first();
            if ($etab) {
                //Etablissement dans Refonte(Structures)
                $struct = Structure::where('libelle_long', strtoupper(Str::ascii($etab->libelle)))
                ->where('libelle_court',strtoupper(str_replace(".","",$etab->sigle)))
                ->first();
                if ($struct) {
                    //Departement dans Refonte(Historique Structure)
                    $dept = Departement::where('libelle_long', strtoupper(Str::ascii(str_replace(".","",$section->libelle_long))))
                        ->where('libelle_court', strtoupper(Str::ascii(str_replace(".","",$section->libelle_court))))
                        ->where('id_structures', $struct->id_structure)
                        ->first();
                    if (!$dept) {
                        try {
                            DB::connection('oracle')->table('Historiques_structures')->insert([
                                'libelle_long' => strtoupper(Str::ascii(str_replace(".","",$section->libelle_long))),
                                'libelle_court' => strtoupper(Str::ascii(str_replace(".","",$section->libelle_court))),
                                'id_structures' => $struct->id_structure,
                                'uti_cree' => 33,
                            ]);
                        } catch (Exception $e) {
                            dump($e->getMessage());
                        }
                    }
                }
            }
        }
        session_commit();
        return back()->with('success', 'Synchronisation Département effectué');
    }
}
