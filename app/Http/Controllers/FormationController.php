<?php

namespace App\Http\Controllers;


use App\Models\Ref\Formation;
use App\Models\Scol\EcoleDoctorale as ScolEcoleDoctorale;
use App\Models\Scol\Departement as ScolDepartement;
use App\Models\Ref\Departement;
use App\Models\Ref\Mention;
use App\Models\Scol\Mention as ScolMention;
use App\Models\Ref\GradeFormation;
use App\Models\Scol\GradeFormation as ScolGradeFormation;
use App\Models\Ref\Cycle;
use App\Models\Scol\Cycle as ScolCycle;
use App\Models\Ref\TypeFormation;
use App\Models\Scol\TypeFormation as ScolTypeFormation;
use App\Models\Ref\OrganisationEtude;
use App\Models\Scol\OrganisationEtude as ScolOrganisationEtude;
use App\Models\Ref\Etablissement;
use App\Models\Scol\Etablissement as ScolEtablissement;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Str;

class FormationController extends Controller
{
    function insertcycle()
    {
    }
    function randomNumberSequence($requiredLength = 6, $highestDigit = 9)
    {
        $sequence = '33 8';
        for ($i = 0; $i < $requiredLength; ++$i) {
            $sequence .= mt_rand(0, $highestDigit);
        }
        return $sequence;
    }

    public function syncFormation()
    {
        /*$str = "Licence profesionnelle en sciences économiques et de gestion, mention sciences de gestion, spécialité marketing, option gestion commerciale";
        if(str_contains($str,", mention")){
            $lib_long = substr($str,0, strpos($str,", mention")) ;
            dump("Libelle $lib_long");
            if(str_contains($str,", spécialité")){
                if(str_contains($str,", option")){
                    $spec = substr($str, strpos($str, ", spécialité")+14, (strlen($str)-strlen($lib_long)+10+strlen()) );
                    dump("Spécialite $spec");
                    $opt = substr($str, strpos($str, ", option")+9 );
                    dd("Option $opt");
                }else{
                    $spec = substr($str, strpos($str, ", spécialité")+14 );
                    dd("Spécialite $spec");
                }
            }
        }*/
        $org_etu_id = '';
        $lib_crt = '';
        $scolFormations = DB::connection('oracleScol')->table('FORMATION_ACCREDITE as fa')
            ->join('FORMATION_ACC_FORMATION as faf', 'fa.CODE_FORMATION', '=', 'faf.ID_FORMATION_ACC')
            ->join('Formation as fr', 'faf.ID_FORMATION', '=', 'fr.CODE_FORMATION')
            ->select('fa.*', 'fr.CODE_ED as CODE_EDOC')
            ->get();
        foreach ($scolFormations as $scolFormation) {
            if (($scolFormation->cycle_code) && ($scolFormation->code_mention) &&
                ($scolFormation->code_grade_diplome) && ($scolFormation->code_departement) && ($scolFormation->libelle_long)
            ) {
                if(str_contains(', mention',$scolFormation->libelle_long) ){
                    dd(str_contains(', mention',$scolFormation->libelle_long));
                }
                $lib_crt = '';
                if (str_contains($scolFormation->libelle_long, '(')) {
                    $pos = strpos($scolFormation->libelle_long, '(');
                    $lib_crt = substr($scolFormation->libelle_long, $pos);
                    //$lib = explode(" ",Str::ascii(substr($scolFormation->libelle_long,0,$pos)));
                } else {
                    $lib = explode(" ", Str::ascii($scolFormation->libelle_long));
                    for ($i = 0; $i < sizeof($lib); $i++) {
                        if (str_contains($lib[$i], "'")) {
                            $lib_crt .= strtoupper(substr($lib[$i], 2, 1));
                        } else if (strlen($lib[$i]) > 3) {
                            $lib_crt .= strtoupper(substr($lib[$i], 0, 1));
                        }
                    }
                }
                if ($scolFormation->code_edoc) {
                    dump($scolFormation);
                    //Insert if not exist in structure and in historique structure
                    $scolEcolDoctorale = ScolEcoleDoctorale::where('code', $scolFormation->code_edoc)->first();
                    if ($scolEcolDoctorale) {
                        $etab = Etablissement::where('libelle_long', strtoupper(Str::ascii($scolEcolDoctorale->libelle_long)))
                            ->where('libelle_court', strtoupper(str_replace(".", "", $scolEcolDoctorale->libelle_court)))
                            ->where('id_type_section', 135)
                            ->first();
                        //dump($etab);
                        if (!$etab) {
                            try {
                                $etb = DB::connection('oracle')->table('structures')->insert([
                                    'libelle_long' => strtoupper(Str::ascii($scolEcolDoctorale->libelle_long)),
                                    'libelle_court' => strtoupper(str_replace(".", "", $scolEcolDoctorale->libelle_court)),
                                    'id_type_section' => 135,
                                    'url' => strtolower('https://www.' . $scolEcolDoctorale->libelle_court . '.sn'),
                                    'email' => strtolower($scolEcolDoctorale->libelle_court . '@ucad.edu.sn'),
                                    'uti_cree' => 33,
                                    'telephone' => $this->randomNumberSequence(),
                                    'adresse' => 'UCAD/Dakar'
                                ]);
                                if ($etb) {
                                    $etab1 = Etablissement::where('libelle_long', strtoupper(Str::ascii($scolEcolDoctorale->libelle_long)))
                                        ->where('libelle_court', strtoupper(str_replace(".", "", $scolEcolDoctorale->libelle_court)))
                                        ->where('id_type_section', 135)
                                        ->first();
                                    //dump($etab1);
                                    $dept = Departement::where('libelle_long', strtoupper(Str::ascii(str_replace(".", "", $scolEcolDoctorale->libelle_long))))
                                        ->where('libelle_court', strtoupper(Str::ascii(str_replace(".", "", $scolEcolDoctorale->libelle_court))))
                                        ->where('id_structures', $etab1->id_structure)
                                        ->first();
                                    //dump($dept);
                                    if (!$dept) {
                                        try {
                                            $dpt = DB::connection('oracle')->table('Historiques_structures')->insert([
                                                'libelle_long' => strtoupper(Str::ascii(str_replace(".", "", $scolEcolDoctorale->libelle_long))),
                                                'libelle_court' => strtoupper(Str::ascii(str_replace(".", "", $scolEcolDoctorale->libelle_court))),
                                                'id_structures' => $etab1->id_structure,
                                                'uti_cree' => 33,
                                            ]);
                                            if ($dpt) {
                                                $dept1 = Departement::where(
                                                    'libelle_long',
                                                    strtoupper(Str::ascii(str_replace(".", "", $scolEcolDoctorale->libelle_long)))
                                                )
                                                    ->where('libelle_court', strtoupper(Str::ascii(str_replace(".", "", $scolEcolDoctorale->libelle_court))))
                                                    ->where('id_structures', $etab1->id_structure)
                                                    ->first();
                                                //dump($dept1);
                                                $scolcycle = ScolCycle::where('code', $scolFormation->cycle_code)->first();
                                                if ($scolcycle) {
                                                    $cycle = Cycle::where('libelle_long', $scolcycle->libelle_long)
                                                        ->where('libelle_court', $scolcycle->libelle_court)->first();
                                                    //dump($cycle);
                                                }
                                                if (empty($scolFormation->type_formation)) :
                                                    $typeFormation = 1;
                                                else :
                                                    $scolTypeFormation = ScolTypeFormation::where('type_formation', $scolFormation->type_formation)->first();
                                                    if ($scolTypeFormation) {
                                                        $typeFormation = TypeFormation::where('libelle_long', $scolTypeFormation->libelle_type_formation)->first();
                                                        // dump($typeFormation);
                                                    }
                                                endif;
                                                $scolMention = ScolMention::where('code_mention', $scolFormation->code_mention)->first();
                                                if ($scolMention) {
                                                    $mention = Mention::where('libelle_long', $scolMention->lib_long_mention)
                                                        ->where('libelle_court', $scolMention->lib_court_mention)->first();
                                                    //dump($mention);
                                                }
                                                $scolGradeFormation = ScolGradeFormation::where('code_grade_diplome', $scolFormation->code_grade_diplome)->first();
                                                if ($scolGradeFormation) {
                                                    $gradeFormation = GradeFormation::where('libelle_long', $scolGradeFormation->libelle_long)
                                                        ->where('libelle_court', $scolGradeFormation->libelle_grade)->first();
                                                    //dump($gradeFormation);
                                                }
                                                if ($dept1 && $gradeFormation && $typeFormation && $cycle && $mention) {
                                                    $formation = Formation::where('libelle_long', $scolFormation->libelle_long)
                                                        ->where('libelle_court', $lib_crt)
                                                        ->where('id_mention', $mention->id_mention)
                                                        ->where('id_cycle', $cycle->id_cycle)
                                                        ->where('id_type_formation', $typeFormation = 1 ? 1 : $typeFormation->id_type_formation)
                                                        ->where('id_grades_formation', $gradeFormation->id_grades_formation)
                                                        ->where('id_historiques_structure', $dept1->id_historiques_structure)
                                                        ->first();
                                                    //dump($formation);
                                                    if (!$formation) {
                                                        try {
                                                            $form = DB::connection('oracle')->table('formations')->insert([
                                                                'LIBELLE_LONG' => $scolFormation->libelle_long,
                                                                'LIBELLE_COURT' => $lib_crt,
                                                                'ID_MENTION' => $mention->id_mention,
                                                                'ID_CYCLE' => $cycle->id_cycle,
                                                                'ID_TYPE_FORMATION' => $typeFormation = 1 ? 1 : $typeFormation->id_type_formation,
                                                                'ID_GRADES_FORMATION' => $gradeFormation->id_grades_formation,
                                                                'ID_HISTORIQUES_STRUCTURE' => $dept1->id_historiques_structure,
                                                                'ID_ORGANISATION_ETUDE' => $org_etu_id,
                                                                'UTI_CREE' => 33,
                                                                'OUVERT' => (!$scolFormation->ouvert ? 0 : 1),
                                                                'PROFESSIONALISANTE' => (!$scolFormation->professionnalisante ? 0 : 1),
                                                                'JUSTIFICATION_PROGRAMME' => $scolFormation->justification_programme,
                                                                'ORGANISATION_PROGRAMME' => $scolFormation->organisation_programme,
                                                                'PROFIL_ACADEMIQUE' => $scolFormation->profil_academique,
                                                                'PROFIL_PROFESSIONNEL' => $scolFormation->profil_professionnel,
                                                                'OBSERVATIONS' => $scolFormation->observations,
                                                                'PAYANTE' => (!$scolFormation->payante ? 0 : 1),
                                                                'VALIDE' => ($scolFormation->valide == 'O' ? 1 : 0),
                                                                'PRESENTIELLE' => $scolFormation->presentielle == 'P' ? 1 : 0,
                                                                'FORMATION_RECONNUE' => (!$scolFormation->formation_reconnue ? 0 : 1)
                                                            ]);

                                                        } catch (Exception $e) {
                                                            dump($e->getMessage());
                                                        }
                                                    }
                                                }
                                            }
                                        } catch (Exception $e) {
                                            dump($e->getMessage());
                                        }
                                    } else {
                                        //insert formation
                                        $scolcycle = ScolCycle::where('code', $scolFormation->cycle_code)->first();
                                        if ($scolcycle) {
                                            $cycle = Cycle::where('libelle_long', $scolcycle->libelle_long)
                                                ->where('libelle_court', $scolcycle->libelle_court)->first();
                                            //dump($cycle);
                                        }
                                        if (empty($scolFormation->type_formation)) :
                                            $typeFormation = 1;
                                        else :
                                            $scolTypeFormation = ScolTypeFormation::where('type_formation', $scolFormation->type_formation)->first();
                                            if ($scolTypeFormation) {
                                                $typeFormation = TypeFormation::where('libelle_long', $scolTypeFormation->libelle_type_formation)->first();
                                                // dump($typeFormation);
                                            }
                                        endif;
                                        $scolMention = ScolMention::where('code_mention', $scolFormation->code_mention)->first();
                                        if ($scolMention) {
                                            $mention = Mention::where('libelle_long', $scolMention->lib_long_mention)
                                                ->where('libelle_court', $scolMention->lib_court_mention)->first();
                                            //dump($mention);
                                        }
                                        $scolGradeFormation = ScolGradeFormation::where('code_grade_diplome', $scolFormation->code_grade_diplome)->first();
                                        if ($scolGradeFormation) {
                                            $gradeFormation = GradeFormation::where('libelle_long', $scolGradeFormation->libelle_long)
                                                ->where('libelle_court', $scolGradeFormation->libelle_grade)->first();
                                            //dump($gradeFormation);
                                        }
                                        if ($dept && $gradeFormation && $typeFormation && $cycle && $mention) {
                                            $formation = Formation::where('libelle_long', $scolFormation->libelle_long)
                                                ->where('libelle_court', $lib_crt)
                                                ->where('id_mention', $mention->id_mention)
                                                ->where('id_cycle', $cycle->id_cycle)
                                                ->where('id_type_formation', $typeFormation = 1 ? 1 : $typeFormation->id_type_formation)
                                                ->where('id_grades_formation', $gradeFormation->id_grades_formation)
                                                ->where('id_historiques_structure', $dept->id_historiques_structure)
                                                ->first();
                                            //dump($formation);
                                            if (!$formation) {
                                                try {
                                                    $form = DB::connection('oracle')->table('formations')->insert([
                                                        'LIBELLE_LONG' => $scolFormation->libelle_long,
                                                        'LIBELLE_COURT' => $lib_crt,
                                                        'ID_MENTION' => $mention->id_mention,
                                                        'ID_CYCLE' => $cycle->id_cycle,
                                                        'ID_TYPE_FORMATION' => $typeFormation = 1 ? 1 : $typeFormation->id_type_formation,
                                                        'ID_GRADES_FORMATION' => $gradeFormation->id_grades_formation,
                                                        'ID_HISTORIQUES_STRUCTURE' => $dept->id_historiques_structure,
                                                        'ID_ORGANISATION_ETUDE' => $org_etu_id,
                                                        'UTI_CREE' => 33,
                                                        'OUVERT' => (!$scolFormation->ouvert ? 0 : 1),
                                                        'PROFESSIONALISANTE' => (!$scolFormation->professionnalisante ? 0 : 1),
                                                        'JUSTIFICATION_PROGRAMME' => $scolFormation->justification_programme,
                                                        'ORGANISATION_PROGRAMME' => $scolFormation->organisation_programme,
                                                        'PROFIL_ACADEMIQUE' => $scolFormation->profil_academique,
                                                        'PROFIL_PROFESSIONNEL' => $scolFormation->profil_professionnel,
                                                        'OBSERVATIONS' => $scolFormation->observations,
                                                        'PAYANTE' => (!$scolFormation->payante ? 0 : 1),
                                                        'VALIDE' => ($scolFormation->valide == 'O' ? 1 : 0),
                                                        'PRESENTIELLE' => $scolFormation->presentielle == 'P' ? 1 : 0,
                                                        'FORMATION_RECONNUE' => (!$scolFormation->formation_reconnue ? 0 : 1)
                                                    ]);
                                                } catch (Exception $e) {
                                                    dump($e->getMessage());
                                                }
                                            }
                                        }
                                    }
                                }
                            } catch (Exception $ex) {
                            }
                        } else {
                            $dept = Departement::where('libelle_long', strtoupper(Str::ascii(str_replace(".", "", $scolEcolDoctorale->libelle_long))))
                                ->where('libelle_court', strtoupper(Str::ascii(str_replace(".", "", $scolEcolDoctorale->libelle_court))))
                                ->where('id_structures', $etab->id_structure)
                                ->first();
                            //dump($dept);
                            if (!$dept) {
                                try {
                                    $dpt = DB::connection('oracle')->table('Historiques_structures')->insert([
                                        'libelle_long' => strtoupper(Str::ascii(str_replace(".", "", $scolEcolDoctorale->libelle_long))),
                                        'libelle_court' => strtoupper(Str::ascii(str_replace(".", "", $scolEcolDoctorale->libelle_court))),
                                        'id_structures' => $etab->id_structure,
                                        'uti_cree' => 33,
                                    ]);
                                    if ($dpt) {
                                        $dept1 = Departement::where('libelle_long', strtoupper(Str::ascii(str_replace(".", "", $scolEcolDoctorale->libelle_long))))
                                            ->where('libelle_court', strtoupper(Str::ascii(str_replace(".", "", $scolEcolDoctorale->libelle_court))))
                                            ->where('id_structures', $etab->id_structure)
                                            ->first();
                                        //dump($dept1);
                                        $scolcycle = ScolCycle::where('code', $scolFormation->cycle_code)->first();
                                        if ($scolcycle) {
                                            $cycle = Cycle::where('libelle_long', $scolcycle->libelle_long)
                                                ->where('libelle_court', $scolcycle->libelle_court)->first();
                                            //dump($cycle);
                                        }
                                        if (empty($scolFormation->type_formation)) :
                                            $typeFormation = 1;
                                        else :
                                            $scolTypeFormation = ScolTypeFormation::where('type_formation', $scolFormation->type_formation)->first();
                                            if ($scolTypeFormation) {
                                                $typeFormation = TypeFormation::where('libelle_long', $scolTypeFormation->libelle_type_formation)->first();
                                                // dump($typeFormation);
                                            }
                                        endif;
                                        $scolMention = ScolMention::where('code_mention', $scolFormation->code_mention)->first();
                                        if ($scolMention) {
                                            $mention = Mention::where('libelle_long', $scolMention->lib_long_mention)
                                                ->where('libelle_court', $scolMention->lib_court_mention)->first();
                                            //dump($mention);
                                        }
                                        $scolGradeFormation = ScolGradeFormation::where('code_grade_diplome', $scolFormation->code_grade_diplome)->first();
                                        if ($scolGradeFormation) {
                                            $gradeFormation = GradeFormation::where('libelle_long', $scolGradeFormation->libelle_long)
                                                ->where('libelle_court', $scolGradeFormation->libelle_grade)->first();
                                            //dump($gradeFormation);
                                        }
                                        if ($dept1 && $gradeFormation && $typeFormation && $cycle && $mention) {
                                            $formation = Formation::where('libelle_long', $scolFormation->libelle_long)
                                                ->where('libelle_court', $lib_crt)
                                                ->where('id_mention', $mention->id_mention)
                                                ->where('id_cycle', $cycle->id_cycle)
                                                ->where('id_type_formation', $typeFormation = 1 ? 1 : $typeFormation->id_type_formation)
                                                ->where('id_grades_formation', $gradeFormation->id_grades_formation)
                                                ->where('id_historiques_structure', $dept1->id_historiques_structure)
                                                ->first();
                                            //dump($formation);
                                            if (!$formation) {
                                                try {
                                                    $form = DB::connection('oracle')->table('formations')->insert([
                                                        'LIBELLE_LONG' => $scolFormation->libelle_long,
                                                        'LIBELLE_COURT' => $lib_crt,
                                                        'ID_MENTION' => $mention->id_mention,
                                                        'ID_CYCLE' => $cycle->id_cycle,
                                                        'ID_TYPE_FORMATION' => $typeFormation = 1 ? 1 : $typeFormation->id_type_formation,
                                                        'ID_GRADES_FORMATION' => $gradeFormation->id_grades_formation,
                                                        'ID_HISTORIQUES_STRUCTURE' => $dept1->id_historiques_structure,
                                                        'ID_ORGANISATION_ETUDE' => $org_etu_id,
                                                        'UTI_CREE' => 33,
                                                        'OUVERT' => (!$scolFormation->ouvert ? 0 : 1),
                                                        'PROFESSIONALISANTE' => (!$scolFormation->professionnalisante ? 0 : 1),
                                                        'JUSTIFICATION_PROGRAMME' => $scolFormation->justification_programme,
                                                        'ORGANISATION_PROGRAMME' => $scolFormation->organisation_programme,
                                                        'PROFIL_ACADEMIQUE' => $scolFormation->profil_academique,
                                                        'PROFIL_PROFESSIONNEL' => $scolFormation->profil_professionnel,
                                                        'OBSERVATIONS' => $scolFormation->observations,
                                                        'PAYANTE' => (!$scolFormation->payante ? 0 : 1),
                                                        'VALIDE' => ($scolFormation->valide == 'O' ? 1 : 0),
                                                        'PRESENTIELLE' => $scolFormation->presentielle == 'P' ? 1 : 0,
                                                        'FORMATION_RECONNUE' => (!$scolFormation->formation_reconnue ? 0 : 1)
                                                    ]);
                                                } catch (Exception $e) {
                                                    dump($e->getMessage());
                                                }
                                            }
                                        }
                                    }
                                } catch (Exception $e) {
                                    dump($e->getMessage());
                                }
                            } else {
                                $scolcycle = ScolCycle::where('code', $scolFormation->cycle_code)->first();
                                if ($scolcycle) {
                                    $cycle = Cycle::where('libelle_long', $scolcycle->libelle_long)
                                        ->where('libelle_court', $scolcycle->libelle_court)->first();
                                    //dump($cycle);
                                }
                                if (empty($scolFormation->type_formation)) :
                                    $typeFormation = 1;
                                else :
                                    $scolTypeFormation = ScolTypeFormation::where('type_formation', $scolFormation->type_formation)->first();
                                    if ($scolTypeFormation) {
                                        $typeFormation = TypeFormation::where('libelle_long', $scolTypeFormation->libelle_type_formation)->first();
                                        // dump($typeFormation);
                                    }
                                endif;
                                $scolMention = ScolMention::where('code_mention', $scolFormation->code_mention)->first();
                                if ($scolMention) {
                                    $mention = Mention::where('libelle_long', $scolMention->lib_long_mention)
                                        ->where('libelle_court', $scolMention->lib_court_mention)->first();
                                    //dump($mention);
                                }
                                $scolGradeFormation = ScolGradeFormation::where('code_grade_diplome', $scolFormation->code_grade_diplome)->first();
                                if ($scolGradeFormation) {
                                    $gradeFormation = GradeFormation::where('libelle_long', $scolGradeFormation->libelle_long)
                                        ->where('libelle_court', $scolGradeFormation->libelle_grade)->first();
                                    //dump($gradeFormation);
                                }
                                if ($dept && $gradeFormation && $typeFormation && $cycle && $mention) {
                                    $formation = Formation::where('libelle_long', $scolFormation->libelle_long)
                                        ->where('libelle_court', $lib_crt)
                                        ->where('id_mention', $mention->id_mention)
                                        ->where('id_cycle', $cycle->id_cycle)
                                        ->where('id_type_formation', $typeFormation = 1 ? 1 : $typeFormation->id_type_formation)
                                        ->where('id_grades_formation', $gradeFormation->id_grades_formation)
                                        ->where('id_historiques_structure', $dept->id_historiques_structure)
                                        ->first();
                                    //dump($formation);
                                    if (!$formation) {
                                        try {
                                            $form = DB::connection('oracle')->table('formations')->insert([
                                                'LIBELLE_LONG' => $scolFormation->libelle_long,
                                                'LIBELLE_COURT' => $lib_crt,
                                                'ID_MENTION' => $mention->id_mention,
                                                'ID_CYCLE' => $cycle->id_cycle,
                                                'ID_TYPE_FORMATION' => $typeFormation = 1 ? 1 : $typeFormation->id_type_formation,
                                                'ID_GRADES_FORMATION' => $gradeFormation->id_grades_formation,
                                                'ID_HISTORIQUES_STRUCTURE' => $dept->id_historiques_structure,
                                                'ID_ORGANISATION_ETUDE' => $org_etu_id,
                                                'UTI_CREE' => 33,
                                                'OUVERT' => (!$scolFormation->ouvert ? 0 : 1),
                                                'PROFESSIONALISANTE' => (!$scolFormation->professionnalisante ? 0 : 1),
                                                'JUSTIFICATION_PROGRAMME' => $scolFormation->justification_programme,
                                                'ORGANISATION_PROGRAMME' => $scolFormation->organisation_programme,
                                                'PROFIL_ACADEMIQUE' => $scolFormation->profil_academique,
                                                'PROFIL_PROFESSIONNEL' => $scolFormation->profil_professionnel,
                                                'OBSERVATIONS' => $scolFormation->observations,
                                                'PAYANTE' => (!$scolFormation->payante ? 0 : 1),
                                                'VALIDE' => ($scolFormation->valide == 'O' ? 1 : 0),
                                                'PRESENTIELLE' => $scolFormation->presentielle == 'P' ? 1 : 0,
                                                'FORMATION_RECONNUE' => (!$scolFormation->formation_reconnue ? 0 : 1)
                                            ]);
                                        } catch (Exception $e) {
                                            dump($e->getMessage());
                                        }
                                    }
                                }
                            }
                        }
                    }
                    //dump($ecoleDoctorale);
                } else {
                    if ($scolFormation->organisation_programme) {
                        $scolOrganisationEtude = ScolOrganisationEtude::where('id_organisation_etude', $scolFormation->organisation_programme)->first();
                        if ($scolOrganisationEtude) {
                            $orgEtude = OrganisationEtude::where('libelle_long', $scolOrganisationEtude->libelle_long)
                                ->where('libelle_court', $scolOrganisationEtude->libelle_court)
                                ->first();
                            if ($orgEtude) {
                                $org_etu_id = $orgEtude->id_organisation_etude;
                            }
                        }
                    }

                    $scolcycle = ScolCycle::where('code', $scolFormation->cycle_code)->first();
                    if ($scolcycle) {
                        $cycle = Cycle::where('libelle_long', $scolcycle->libelle_long)
                            ->where('libelle_court', $scolcycle->libelle_court)->first();
                    }
                    if (empty($scolFormation->type_formation)) :
                        $typeFormation = 1;
                    else :
                        $scolTypeFormation = ScolTypeFormation::where('type_formation', $scolFormation->type_formation)->first();
                        if ($scolTypeFormation) {
                            $typeFormation = TypeFormation::where('libelle_long', $scolTypeFormation->libelle_type_formation)->first();
                            //dump('Type Formation '.$typeFormation);
                        }
                    endif;
                    $scolMention = ScolMention::where('code_mention', $scolFormation->code_mention)->first();
                    if ($scolMention) {
                        $mention = Mention::where('libelle_long', $scolMention->lib_long_mention)
                            ->where('libelle_court', $scolMention->lib_court_mention)->first();
                        //dump('Mention '.$mention);
                    }
                    $scolGradeFormation = ScolGradeFormation::where('code_grade_diplome', $scolFormation->code_grade_diplome)->first();
                    if ($scolGradeFormation) {
                        $gradeFormation = GradeFormation::where('libelle_long', $scolGradeFormation->libelle_long)
                            ->where('libelle_court', $scolGradeFormation->libelle_grade)->first();
                        //dump('Grade Formation '.$gradeFormation);
                    }
                    $scolDepartement = ScolDepartement::where('code', $scolFormation->code_departement)
                        ->where('type_section', '!=', null)
                        ->first();

                    if ($scolDepartement->etablissem_code) {
                        $scolStruct = ScolEtablissement::where('code', $scolDepartement->etablissem_code)->first();
                        if ($scolStruct) {
                            $struct = Etablissement::where('libelle_long', strtoupper(Str::ascii($scolStruct->libelle)))
                                ->where('libelle_court', strtoupper(str_replace(".", "", $scolStruct->sigle)))
                                ->first();
                            //dump($struct);
                            if ($struct) {
                                $departement = Departement::where('libelle_long', strtoupper(Str::ascii(str_replace(".", "", $scolDepartement->libelle_long))))
                                    ->where('libelle_court', strtoupper(Str::ascii(str_replace(".", "", $scolDepartement->libelle_court))))
                                    ->where('id_structures', $struct->id_structure)
                                    ->first();
                                //dump($departement);
                                if ($departement && $gradeFormation && $typeFormation && $cycle && $mention) {
                                    $formation = Formation::where('libelle_long', $scolFormation->libelle_long)
                                        ->where('libelle_court', $lib_crt)
                                        ->where('id_mention', $mention->id_mention)
                                        ->where('id_cycle', $cycle->id_cycle)
                                        ->where('id_type_formation', $typeFormation = 1 ? 1 : $typeFormation->id_type_formation)
                                        ->where('id_grades_formation', $gradeFormation->id_grades_formation)
                                        ->where('id_historiques_structure', $departement->id_historiques_structure)
                                        ->first();
                                    if (!$formation) {
                                        try {
                                            DB::connection('oracle')->table('formations')->insert([
                                                'LIBELLE_LONG' => $scolFormation->libelle_long,
                                                'LIBELLE_COURT' => $lib_crt,
                                                'ID_MENTION' => $mention->id_mention,
                                                'ID_CYCLE' => $cycle->id_cycle,
                                                'ID_TYPE_FORMATION' => $typeFormation = 1 ? 1 : $typeFormation->id_type_formation,
                                                'ID_GRADES_FORMATION' => $gradeFormation->id_grades_formation,
                                                'ID_HISTORIQUES_STRUCTURE' => $departement->id_historiques_structure,
                                                'ID_ORGANISATION_ETUDE' => $org_etu_id,
                                                'UTI_CREE' => 33,
                                                'OUVERT' => (!$scolFormation->ouvert ? 0 : 1),
                                                'PROFESSIONALISANTE' => (!$scolFormation->professionnalisante ? 0 : 1),
                                                'JUSTIFICATION_PROGRAMME' => $scolFormation->justification_programme,
                                                'ORGANISATION_PROGRAMME' => $scolFormation->organisation_programme,
                                                'PROFIL_ACADEMIQUE' => $scolFormation->profil_academique,
                                                'PROFIL_PROFESSIONNEL' => $scolFormation->profil_professionnel,
                                                'OBSERVATIONS' => $scolFormation->observations,
                                                'PAYANTE' => (!$scolFormation->payante ? 0 : 1),
                                                'VALIDE' => ($scolFormation->valide == 'O' ? 1 : 0),
                                                'PRESENTIELLE' => $scolFormation->presentielle == 'P' ? 1 : 0,
                                                'FORMATION_RECONNUE' => (!$scolFormation->formation_reconnue ? 0 : 1)
                                            ]);
                                        } catch (Exception $e) {
                                            dump($e->getMessage());
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        session_commit();
        return back()->with('success', 'Synchronisation Formations effectué');
    }
}
