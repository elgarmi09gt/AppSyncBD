<?php

namespace App\Http\Controllers;

use App\Models\Ref\Etablissement as Structure;
use App\Models\Ref\TypeSection;
use App\Models\Scol\Etablissement;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EtablissementController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    function randomNumberSequence($requiredLength = 6, $highestDigit = 9) {
        $sequence = '33 8';
        for ($i = 0; $i < $requiredLength; ++$i) {
            $sequence .= mt_rand(0, $highestDigit);
        }
        return $sequence;
    }

    public function synchEtablissement(){
        $etabScol = Etablissement::all();
        foreach($etabScol as $etabsc){
            //dump(mb_strtoupper($etabsc->libelle));
            $is_etab_exist = Structure::where('libelle_long', mb_strtoupper($etabsc->libelle))
            ->orwhere('libelle_court',mb_strtoupper(str_replace(".","",$etabsc->sigle)))
            ->count();
            if($is_etab_exist == 0){
                echo strtolower(str_replace(".","",$etabsc->sigle))."@ucad.edu.sn<br>";
                $type = TypeSection::where('libelle_long',$etabsc->type_etab)->first();
                if($type != null){
                    //$struct = new Structure();
                    $id_type_section = $type->id_type_section;
                    $uticree = 33;
                    $signature = $etabsc->signature;
                    $url = "https://www.".strtolower(str_replace(".","",$etabsc->sigle)).".sn";
                    $email = strtolower(str_replace(".","",$etabsc->sigle))."@ucad.edu.sn";
                    if($etabsc->etab_parent){
                        $parentScol = Etablissement::where("code",$etabsc->etab_parent)->first();
                        $parentRef = Structure::where('libelle_long',$parentScol->libelle)->first();
                        if($parentRef){ $structure_parent = $parentRef->id_structure; }
                        else{ $structure_parent = null;}
                    }
                    if(!$etabsc->telephone){
                        $sequence = '33 8';
                        for ($i = 0; $i < 6; ++$i) {
                            $sequence .= mt_rand(0, 9);
                        }
                        $telephone=$sequence;
                    }
                    else{$telephone = $etabsc->telephone;}
                    if(!$etabsc->adresse){ $adresse = "UCAD/Dakar"; }
                    else{ $adresse = $etabsc->adresse; }
                    try{
                        DB::connection('oracle')->table('structures')->insert([
                        'libelle_long' => mb_strtoupper($etabsc->libelle),
                        'libelle_court' =>mb_strtoupper(str_replace(".","",$etabsc->sigle)) ,
                        'id_type_section'=>$id_type_section,
                        'url'=>$url,
                        'email'=>$email,
                        'uti_cree'=>$uticree,
                        //'signature'=>$signature,
                        'structure_parent'=>$structure_parent,
                        'telephone'=>$telephone,
                        'adresse'=>$adresse,
                    ]);
                    }catch(Exception $ex){
                        dump($ex);
                    }
                }else{
                    echo $etabsc->type_etab. "Not exist in refonte";
                }
            }else{
                echo "Updating Etablissement ...";
            }
            //dump($struct);
        }
        session_commit();
        return back()->with('success', 'Synchronisation Etablissement effectué');
    }

}
