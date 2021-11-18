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
        //dd(mb_strtoupper('è'));
        //Département dans Scolarite
        $sections = Section::all();
        /*where('type_section','!=',null)
        ->where('ETABLISSEM_CODE','A')
        ->get();
        echo "Département dans Scolarite avec type_section";
        dump($sections);
        $sections1 = Section::where('ETABLISSEM_CODE','A')
        ->get();
        echo "Département dans Scolarite pas type_section";
        dump($sections1);*/
        foreach ($sections as $section) {
            if(str_starts_with($section->libelle_long, '..')){
                $section->libelle_long = "Tron Commun";
                $section->libelle_court = "Tron Commun";
            }
            //Etablissement dans Scolarité
            $etab = Etablissement::where('code', $section->etablissem_code)->first();
            /*echo "Etablissement dans Scolarité";
            dump($etab);*/
            if ($etab) {
                //Etablissement dans Refonte(Structures)
                $struct = Structure::where('libelle_long', mb_strtoupper($etab->libelle))
                ->where('libelle_court',mb_strtoupper(str_replace(".","",$etab->sigle)))
                ->first();
                /*echo " Etablissement dans Refonte ";
                dump($struct);*/
                if ($struct) {
                    //Departement dans Refonte(Historique Structure)
                    $dept = Departement::where('libelle_long', mb_strtoupper(str_replace(".","",$section->libelle_long)))
                        ->where('libelle_court', mb_strtoupper(str_replace(".","",$section->libelle_court)))
                        ->where('id_structures', $struct->id_structure)
                        ->first();
                    /*echo "Departement dans Refonte";
                    dump($dept);*/
                    if (!$dept) {
                        try {
                            DB::connection('oracle')->table('Historiques_structures')->insert([
                                'libelle_long' => mb_strtoupper(str_replace(".","",$section->libelle_long)),
                                'libelle_court' => mb_strtoupper(str_replace(".","",$section->libelle_court)),
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
