<?php

namespace App\Http\Controllers;

use App\Models\Ref\TypeSection;
use App\Models\Scol\Etablissement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TypeSectionController extends Controller
{
    public function synchTypeSection(){
        $etabScol = Etablissement::all();
        foreach($etabScol as $etabsc){
            $type = TypeSection::where('libelle_long',$etabsc->type_etab)
            ->where('libelle_court',$etabsc->type_etab)
            ->first();
            //dump($type);
            if(!$type){
                DB::connection('oracle')->table('type_sections')->insert([
                    'libelle_long' => $etabsc->type_etab,
                    'libelle_court' =>$etabsc->type_etab ,
                    'uti_cree'=>33
                ]);
            }/*else{
                DB::connection('oracle')->table('type_sections')->update([
                    'libelle_long' => $etabsc->type_etab,
                    'libelle_court' =>$etabsc->type_etab ,
                    'uti_modifie'=>1
                ]);
            }*/
        }
        session_commit();
        return back()->with('success', 'Synchronisation Type Etablissement effectué');
    }
}
